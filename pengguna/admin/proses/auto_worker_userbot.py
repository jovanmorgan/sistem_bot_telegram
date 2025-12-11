import pymysql
import asyncio
import json
from telethon import TelegramClient
from datetime import datetime
import pytz
import os
import sys
import traceback

# ----------- Telethon Config ----------
api_id = 39282408
api_hash = "5d594a30013287cb4eae7c1caefd4d56"
session_file = "session_user"  # nama session file

# Gunakan session yang sudah login
client = TelegramClient(session_file, api_id, api_hash)

# ----------- Timezone Bali (WITA) ----------
tz = pytz.timezone("Asia/Makassar")

# ----------- Database ----------
db = pymysql.connect(
    host="localhost",
    user="root",
    password="",
    database="bot_cnet",
    cursorclass=pymysql.cursors.DictCursor
)

async def run_worker():

    # Jalankan Telethon
    async with client:
        log_text = ""
        now_time = datetime.now(tz).strftime("%H:%M:%S")
        log_text += f"\n========== AUTO MESSAGE START [{now_time}] ==========\n"

        # Ambil pengaturan auto message
        with db.cursor() as cur:
            cur.execute("SELECT auto_kirim, group_id, pesan_list FROM pengaturan_auto LIMIT 1")
            data = cur.fetchone()

        # Jika auto_kirim off → STOP worker langsung
        if not data or data["auto_kirim"] != "on":
            log_text += "AUTO KIRIM OFF - Worker dihentikan.\n"

            # Pastikan Telethon disconnect agar tidak lock session
            try:
                await client.disconnect()
            except:
                pass

            # Jangan hapus session saat stop normal
            log_text += "⛔ Session tidak dihapus (STOP manual)\n"

            return {"stop": True, "log": log_text}

        # Ambil target & pesan
        try:
            targets = json.loads(data["group_id"])
            pesan_list = json.loads(data["pesan_list"])
        except Exception:
            log_text += "Format JSON target atau pesan salah!\n"

            try:
                await client.disconnect()
            except:
                pass

            return {"stop": True, "log": log_text}

        if not targets:
            log_text += "TIDAK ADA TARGET TUJUAN!\n"

            try:
                await client.disconnect()
            except:
                pass

            return {"stop": True, "log": log_text}

        if not pesan_list:
            log_text += "TIDAK ADA PESAN YANG TERDAFTAR!\n"

            try:
                await client.disconnect()
            except:
                pass

            return {"stop": True, "log": log_text}

        # Kirim semua pesan berdasarkan pesan_list
        for pesan_id in pesan_list:

            # CEK auto_kirim setiap saat
            with db.cursor() as cur:
                cur.execute("SELECT auto_kirim FROM pengaturan_auto LIMIT 1")
                status = cur.fetchone()

            if status["auto_kirim"] != "on":
                log_text += "❌ AUTO KIRIM OFF saat pengiriman, worker dihentikan!\n"

                try:
                    await client.disconnect()
                except:
                    pass

                log_text += "⛔ Session tidak dihapus (STOP manual)\n"
                return {"stop": True, "log": log_text}

            # Ambil isi pesan
            with db.cursor() as cur:
                cur.execute("SELECT pesan FROM pesan_broadcast WHERE id=%s", (pesan_id,))
                d = cur.fetchone()

            if not d:
                log_text += f"⚠ Pesan ID {pesan_id} tidak ditemukan, dilewati.\n"
                continue

            pesan = d["pesan"]

            # Kirim ke semua target
            for chat_id in targets:
                try:
                    chat_id_int = int(chat_id)
                    entity = await client.get_entity(chat_id_int)
                    await client.send_message(entity, pesan)
                    log_text += f"✔ Berhasil kirim pesan ID {pesan_id} ke {chat_id_int}\n"

                except Exception as e:
                    log_text += f"❌ Gagal kirim pesan ID {pesan_id} ke {chat_id}: {e}\n"

        now_time = datetime.now(tz).strftime("%H:%M:%S")
        log_text += f"\n========== SELESAI [{now_time}] ==========\n"

        # Pastikan disconnect agar telethon tidak mengunci session
        try:
            await client.disconnect()
        except:
            pass

        return {"stop": False, "log": log_text}


# ------------ MAIN PROGRAM ------------
if __name__ == "__main__":
    try:
        result = asyncio.run(run_worker())

        # Cetak JSON aman UTF-8
        output = json.dumps(result, ensure_ascii=False, indent=2)
        print(output.encode(sys.stdout.encoding, errors='replace').decode(sys.stdout.encoding))

    except Exception as e:
        tb = traceback.format_exc()
        print(json.dumps({
            "stop": True,
            "log": f"Worker error: {e}\nTraceback:\n{tb}"
        }, ensure_ascii=False, indent=2))
