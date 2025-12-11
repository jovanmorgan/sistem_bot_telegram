import json
import asyncio
from telethon import TelegramClient

api_id = 39282408
api_hash = "5d594a30013287cb4eae7c1caefd4d56"

# WAJIB: gunakan path ABSOLUTE
session_file = r"C:\xampp\htdocs\sistem_bot_telegram\session_user"

async def get_chats():
    output = {
        "grup": {},
        "channel": {},
        "user": {},
        "error": None
    }

    try:
        client = TelegramClient(session_file, api_id, api_hash)
        await client.connect()

        # Cek login
        if not await client.is_user_authorized():
            output["error"] = "SESSION_NOT_LOGIN"
            print(json.dumps(output))
            return

        dialogs = await client.get_dialogs()

        for d in dialogs:
            try:
                if d.is_group:
                    output["grup"][str(d.id)] = d.name or "Group Tanpa Nama"

                elif d.is_channel:
                    output["channel"][str(d.id)] = d.name or "Channel Tanpa Nama"

                elif d.is_user:
                    nama = ((d.entity.first_name or "") + " " +
                            (d.entity.last_name or "")).strip()
                    output["user"][str(d.id)] = nama or "User Tanpa Nama"

            except:
                continue

        print(json.dumps(output))

    except Exception as e:
        output["error"] = str(e)
        print(json.dumps(output))


if __name__ == "__main__":
    asyncio.run(get_chats())
