from telethon import TelegramClient

api_id = 39282408
api_hash = "5d594a30013287cb4eae7c1caefd4d56"
phone = "+6281339860000"

client = TelegramClient("session_user", api_id, api_hash)

target_group = -1002055329551

message_text = """
‼ SIMPATI DATA TERBAIK PROMO ‼
==================================

♦ STP3GB   = 27.085  🔥🔥🔥 #Turun
Data Reguler 3 GB 30 Hari

♦ STP8GB   = 63.165   🔥🔥🔥 #Turun
Data Reguler 8 GB 30 Hari

♦ STP13GB   = 90.225  🔥🔥🔥 #Turun
Data Reguler 13 GB 30 Hari


‼ SIMPATI DATA TERBAIK ‼
==========================

♦ STU3GB   = 29.700  
Data Reguler 3 GB 30 Hari

♦ STU8GB   = 69.100   
Data Reguler 8 GB 30 Hari

♦ STU13GB   = 98.650  
Data Reguler 13 GB 30 Hari

♦ STU1H   = 9.985 🆕
Data Reguler 1.5 GB 1 Hari

♦ STU3H   = 24.775 🆕
Data Reguler 3.5 GB 3 Hari

♦ STU30GB   = 118.460 🆕
Data Reguler 30 GB 30 Hari

♦ STU50GB   = 177.635 🆕
Data Reguler 50 GB 30 Hari

♦ STU75GB   = 197.575 🆕
Data Reguler 75 GB 30 Hari

♦ STU100GB   = 246.695 🆕
Data Reguler 100 GB 30 Hari


‼ SIMPATI DATA LONG VALIDITY‼
===============================

♦ DT90G3M   = 221.825 🆕
Kuota 90GB (30GB per bulan selama 3 bulan) + Voice + SMS 90 Hari

♦ DT180G6M   = 418.825 🆕
Kuota 180GB (30GB per bulan selama 6 bulan) + Voice + SMS 180 Hari

♦ DT360G12M   = 812.825 🆕
Kuota 360GB (30GB per bulan selama 12 bulan) + Voice + SMS 360 Hari


*Stock Aman Gaspol
*Jalur Modchan
*Speed Wusss
*Bukan Barang GIFT
*Full Kuota Utama Nasional
*Tanpa Syarat Semua Nomor Telkomsel
*Rekon 2 Jam
*CS 24/7

==== CUSTOMER SERVICE ====

• TELEGRAM •
@Cs_Centrumnet

• WEBREPORT •
http://119.13.100.58:8088/

• IRS MARKET MEMBER •
https://member.irsmarket.com/supplier/1024

• DIGIFLAZZ MEMBER •
https://digiflazz.com/seller/oJzzBo
"""

async def main():
    await client.start(phone=phone)
    entity = await client.get_entity(target_group)
    await client.send_message(entity, message_text)
    print("✔ Pesan terkirim ke", target_group)

with client:
    client.loop.run_until_complete(main())
