import pymysql
import asyncio
import json
from telethon import TelegramClient
from datetime import datetime
import pytz
import traceback
import sys

# ----------- Telethon Config ----------
api_id = 39282408
api_hash = "5d594a30013287cb4eae7c1caefd4d56"
session_file = "../session_user"  # sesuaikan path jika perlu

client = TelegramClient(session_file, api_id, api_hash)

# ----------- Timezone ----------
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

    # CEK SESSION LOGIN
    try:
        await client.connect()
        if not await client.is_user_authorized():
            return {
                "stop": True,
                "log": "ERROR: SESSION_NOT_LOGIN - silakan login ulang userbot."
            }
    except Exception as e:
        return {"stop": True, "log": f"ERROR: Telethon gagal connect: {e}"}

    async with client:
        log_text = ""
        now_time = datetime.now(tz).strftime("%H:%M:%S")
        log_text += f"\n== AUTO MESSAGE START [{now_time}] ==\n"

        # Ambil pengaturan
        with db.cursor() as cur:
            cur.execute("SELECT auto_kirim, group_id, pesan_list FROM pengaturan_auto LIMIT 1")
            data = cur.fetchone()

        if not data or data["auto_kirim"] != "on":
            log_text += "AUTO KIRIM OFF - Worker dihentikan.\n"
            await client.disconnect()
            return {"stop": True, "log": log_text}

        # Parse JSON target & pesan
        try:
            targets = json.loads(data["group_id"])
            pesan_list = json.loads(data["pesan_list"])
        except Exception:
            log_text += "ERROR: Format JSON target atau pesan salah!\n"
            await client.disconnect()
            return {"stop": True, "log": log_text}

        if not targets:
            log_text += "TIDAK ADA TARGET TUJUAN!\n"
            await client.disconnect()
            return {"stop": True, "log": log_text}

        if not pesan_list:
            log_text += "TIDAK ADA PESAN YANG TERDAFTAR!\n"
            await client.disconnect()
            return {"stop": True, "log": log_text}

        # Kirim semua pesan berdasarkan pesan_list
        for pesan_id in pesan_list:

            # CEK auto_kirim setiap saat
            with db.cursor() as cur:
                cur.execute("SELECT auto_kirim FROM pengaturan_auto LIMIT 1")
                status = cur.fetchone()

            if status["auto_kirim"] != "on":
                log_text += "AUTO KIRIM OFF saat pengiriman, worker dihentikan!\n"
                await client.disconnect()
                return {"stop": True, "log": log_text}

            # Ambil isi pesan
            with db.cursor() as cur:
                cur.execute("SELECT pesan FROM pesan_broadcast WHERE id=%s", (pesan_id,))
                d = cur.fetchone()

            if not d:
                log_text += f"Pesan ID {pesan_id} tidak ditemukan, dilewati.\n"
                continue

            pesan = d["pesan"]

            # Kirim ke semua target (tangkap error per-target supaya loop terus berjalan)
            for chat_id in targets:
                try:
                    entity = await client.get_entity(int(chat_id))
                    await client.send_message(entity, pesan)
                    log_text += f"[OK] Pesan ID {pesan_id} terkirim ke {chat_id}\n"
                except Exception as e:
                    # CATAT ERROR KE LOG (jangan print di luar JSON)
                    log_text += f"[ERR] Gagal kirim ke {chat_id}: {str(e)}\n"

        now_time = datetime.now(tz).strftime("%H:%M:%S")
        log_text += f"\n======= SELESAI [{now_time}] =======\n"

        await client.disconnect()

        return {"stop": False, "log": log_text}


# MAIN
if __name__ == "__main__":
    try:
        result = asyncio.run(run_worker())

        # --- PENTING: gunakan ensure_ascii=True agar output JSON hanya ASCII ---
        safe = json.dumps(result, ensure_ascii=True)
        print(safe)

    except Exception as e:
        tb = traceback.format_exc()
        error_json = {
            "stop": True,
            "log": f"Worker ERROR: {e}\n\nTraceback:\n{tb}"
        }
        # juga pastikan ASCII-only
        print(json.dumps(error_json, ensure_ascii=True))
