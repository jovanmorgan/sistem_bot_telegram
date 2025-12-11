from telethon import TelegramClient, events
from flask import Flask, jsonify, render_template_string
import asyncio
from datetime import datetime

# ==========================
# CONFIG
# ==========================
api_id = 39282408
api_hash = "5d594a30013287cb4eae7c1caefd4d56"
phone = "+6281339860000"
channel_username = "INFO_INDONESIARELOAD"

# Telegram Client
client = TelegramClient("session_user", api_id, api_hash)

# Flask App
app = Flask(__name__)

# Global storage untuk disajikan di web
cached_messages = []


# ==========================
# LOGIN USER
# ==========================
async def login():
    await client.start(phone=phone)
    print("✔ Login user berhasil")


# ==========================
# DATE START THIS MONTH
# ==========================
def this_month_start():
    now = datetime.now()
    return datetime(now.year, now.month, 1)


# ==========================
# SCRAPE ONLY THIS MONTH
# ==========================
async def scrape_this_month():
    global cached_messages
    cached_messages = []

    start_date = this_month_start()
    print(f"Mengambil pesan dari {start_date} sampai hari ini...\n")

    async for msg in client.iter_messages(channel_username):

        start_date_tz = start_date.replace(tzinfo=msg.date.tzinfo)

        if msg.date < start_date_tz:
            break

        text = msg.message or "(non-text message)"
        time = msg.date.strftime("%Y-%m-%d %H:%M:%S")

        cached_messages.append({
            "id": msg.id,
            "text": text,
            "time": time
        })

    print(f"✔ Total pesan bulan ini: {len(cached_messages)}")


# ==========================
# REALTIME LISTENER
# ==========================
@client.on(events.NewMessage(chats=channel_username))
async def realtime(event):
    global cached_messages

    text = event.message.message or "(non-text)"
    time = event.message.date.strftime("%Y-%m-%d %H:%M:%S")

    print(f"\n=== PESAN BARU ===\n{time} : {text}")

    cached_messages.insert(0, {
        "id": event.message.id,
        "text": text,
        "time": time
    })


# ==========================
# FLASK ROUTES
# ==========================
@app.route("/")
def home_page():
    html = """
    <h2>Telegram Scraper</h2>
    <p>Pilih tampilan:</p>
    <ul>
        <li><a href='/messages'>Lihat JSON</a></li>
        <li><a href='/view'>Lihat dalam bentuk HTML</a></li>
    </ul>
    """
    return render_template_string(html)


@app.route("/messages")
def api_messages():
    return jsonify(cached_messages)


@app.route("/view")
def html_view():
    html = """
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Scraped Messages</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
              rel="stylesheet">

        <style>
            .msg-card {
                border-radius: 12px;
                padding: 15px;
                margin-bottom: 15px;
                background: #fff7e6;
                border-left: 6px solid #ff8800;
                box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
            }
            .msg-time {
                font-size: 12px;
                color: #6c757d;
            }
            .msg-text {
                white-space: pre-wrap;
            }
        </style>
    </head>

    <body class="bg-light">

        <div class="container py-4">

            <h2 class="mb-3">📩 Pesan Telegram Bulan Ini</h2>
            <p class="text-muted">Data otomatis diambil dari channel Telegram.</p>

            <div class="row">
                <div class="col-md-12">

                    {% if messages|length == 0 %}
                        <div class="alert alert-warning text-center">
                            Tidak ada pesan bulan ini.
                        </div>
                    {% endif %}

                    {% for msg in messages %}
                    <div class="msg-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <b>ID Pesan:</b> {{ msg.id }}
                            </div>
                            <div class="msg-time">{{ msg.time }}</div>
                        </div>

                        <div class="msg-text mt-2">{{ msg.text }}</div>
                    </div>
                    {% endfor %}

                    <br>
                    <a href="/" class="btn btn-secondary mt-3">⬅ Kembali ke Home</a>

                </div>
            </div>
        </div>

    </body>
    </html>
    """

    return render_template_string(html, messages=cached_messages)

# ==========================
# START TELEGRAM + FLASK
# ==========================
async def start_all():
    await login()
    await scrape_this_month()

    print("✔ Menunggu pesan baru...")

    # Jalankan Flask tanpa menghentikan Telethon
    asyncio.get_running_loop().run_in_executor(
        None, lambda: app.run(host="0.0.0.0", port=5000, debug=False)
    )

    await client.run_until_disconnected()


with client:
    client.loop.run_until_complete(start_all())
