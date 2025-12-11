from telethon import TelegramClient
import os

# =============== CONFIG ===============
api_id = 39282408
api_hash = "5d594a30013287cb4eae7c1caefd4d56"
phone_number = "+6281339860000"

# Nama file session global
SESSION_FILE = "session_user"
# =======================================


def get_client():
    """
    Mengembalikan TelegramClient yang sudah login.
    Jika session belum ada → login 1x (kode OTP).
    Jika session sudah ada → langsung auto-login.
    """

    client = TelegramClient(SESSION_FILE, api_id, api_hash)

    async def _start():
        if not os.path.exists(SESSION_FILE + ".session"):
            print("Session belum ada → login diperlukan...")
            await client.start(phone=phone_number)
            print("Login sukses & session disimpan!")
        else:
            await client.start()
            print("Session ditemukan → auto-login berhasil.")

    # Jalankan start async
    client.loop.run_until_complete(_start())
    return client

# Test saat file dijalankan langsung
if __name__ == "__main__":
    client = get_client()
    print("Client siap dipakai secara global.")
