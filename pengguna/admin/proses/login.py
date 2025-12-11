from telethon import TelegramClient

api_id = 39282408
api_hash = "5d594a30013287cb4eae7c1caefd4d56"
phone = "+6281339860000"

client = TelegramClient("session_user", api_id, api_hash)

async def main():
    await client.start(phone)
    print("Login sukses!")

with client:
    client.loop.run_until_complete(main())
