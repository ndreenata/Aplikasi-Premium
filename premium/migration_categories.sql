-- ═══════════════════════════════════
-- MIGRATION: Product Categorization
-- Adds 9 categories and ~60 products
-- ═══════════════════════════════════

-- ═══ UPDATE EXISTING PRODUCTS CATEGORIES ═══

-- Netflix, Disney+, Viu, WeTV, Prime Video, HBO → streaming
UPDATE products SET category='streaming' WHERE name LIKE '%Netflix%';
UPDATE products SET category='streaming' WHERE name LIKE '%Disney%';
UPDATE products SET category='streaming' WHERE name LIKE '%Viu%';
UPDATE products SET category='streaming' WHERE name LIKE '%WeTV%';
UPDATE products SET category='streaming' WHERE name LIKE '%Prime Video%';
UPDATE products SET category='streaming' WHERE name LIKE '%HBO%';

-- YouTube → video
UPDATE products SET category='video' WHERE name LIKE '%YouTube%';

-- Spotify → music
UPDATE products SET category='music' WHERE name LIKE '%Spotify%';

-- Canva, VSCO, Lightroom, Alight Motion → design (split appropriately)
UPDATE products SET category='design' WHERE name LIKE '%Canva%';
UPDATE products SET category='editing' WHERE name LIKE '%VSCO%';
UPDATE products SET category='editing' WHERE name LIKE '%Lightroom%';
UPDATE products SET category='editing' WHERE name LIKE '%Alight Motion%';

-- CapCut → editing
UPDATE products SET category='editing' WHERE name LIKE '%CapCut%';

-- ChatGPT, Zoom, Google Drive, Microsoft 365 → productivity
UPDATE products SET category='productivity' WHERE name LIKE '%ChatGPT%';
UPDATE products SET category='productivity' WHERE name LIKE '%Zoom%';
UPDATE products SET category='productivity' WHERE name LIKE '%Google Drive%';
UPDATE products SET category='productivity' WHERE name LIKE '%Microsoft%';


-- ═══ ADD NEW PRODUCTS ═══

-- DESIGN
INSERT IGNORE INTO products (name, description, price, category) VALUES
('Ibis Paint X Pro', 'Aplikasi menggambar digital pro. Brush premium, filter, dan tools lengkap tanpa iklan.', 20000, 'design'),
('PicsArt Gold', 'Edit foto & video dengan AI tools, sticker premium, dan template eksklusif.', 22000, 'design'),
('Gamma AI Pro', 'Buat presentasi AI-powered dengan desain profesional otomatis.', 30000, 'design'),
('Meitu VIP', 'Edit foto dengan filter AI premium, retouch wajah, dan efek kecantikan pro.', 18000, 'design');

-- EDITING
INSERT IGNORE INTO products (name, description, price, category) VALUES
('InShot Pro', 'Edit video & foto tanpa watermark. Efek, filter, dan musik premium.', 20000, 'editing'),
('Remini Premium', 'Tingkatkan kualitas foto buram jadi HD dengan AI enhancer premium.', 18000, 'editing'),
('Gemini Advanced', 'AI assistant Google dengan Gemini Ultra. Analisis, coding, dan kreativitas tanpa batas.', 50000, 'editing');

-- MUSIC
INSERT IGNORE INTO products (name, description, price, category) VALUES
('Apple Music', 'Streaming musik 100 juta lagu, Spatial Audio, Lossless tanpa iklan.', 30000, 'music'),
('TikTok Music Premium', 'Streaming musik dari TikTok tanpa iklan, offline mode, kualitas tinggi.', 18000, 'music');

-- STREAMING
INSERT IGNORE INTO products (name, description, price, category) VALUES
('iflix Premium', 'Nonton film & series Asia tanpa iklan dengan subtitle Indonesia.', 18000, 'streaming'),
('iQIYI VIP', 'Drama China & Korea terbaru, variety show, dan anime premium.', 22000, 'streaming'),
('Mango TV Premium', 'Konten hiburan China terlengkap: drama, variety, dan reality show.', 18000, 'streaming'),
('Youku Premium', 'Streaming drama & film China dengan subtitle multi-bahasa.', 20000, 'streaming'),
('DramaBox Premium', 'Short drama vertikal premium tanpa iklan dan download offline.', 15000, 'streaming'),
('Drakor ID Premium', 'Nonton drama Korea & Asia dengan subtitle Indo, tanpa iklan.', 18000, 'streaming');

-- VIDEO
INSERT IGNORE INTO products (name, description, price, category) VALUES
('ReelShort Premium', 'Short drama premium tanpa iklan, akses episode awal, dan download.', 15000, 'video'),
('ShortMax Premium', 'Nonton short series premium tanpa iklan dengan kualitas HD.', 15000, 'video'),
('Bilibili Premium', 'Anime, donghua, dan konten kreator tanpa iklan di Bilibili.', 20000, 'video'),
('Vision+ Premium', 'Streaming konten original Indonesia, film, dan olahraga premium.', 22000, 'video');

-- LEARNING
INSERT IGNORE INTO products (name, description, price, category) VALUES
('Duolingo Super', 'Belajar bahasa tanpa iklan, lives tak terbatas, dan progress tracking.', 25000, 'learning'),
('Fizzo Novel VIP', 'Baca novel premium tanpa batas. Akses ribuan judul eksklusif.', 15000, 'learning');

-- PRODUCTIVITY
INSERT IGNORE INTO products (name, description, price, category) VALUES
('WPS Office Pro', 'Office suite lengkap tanpa iklan: Docs, Spreadsheet, PDF, Cloud.', 20000, 'productivity'),
('GetContact Premium', 'Lihat siapa yang menelepon, cek identitas nomor, dan blokir spam.', 18000, 'productivity'),
('CamScanner Premium', 'Scan dokumen HD, OCR, tanda tangan digital, dan cloud sync.', 18000, 'productivity');

-- SECURITY
INSERT IGNORE INTO products (name, description, price, category) VALUES
('NordVPN Premium', 'VPN cepat & aman. 5000+ server, no-log policy, enkripsi militer.', 25000, 'security'),
('ExpressVPN Premium', 'VPN tercepat di dunia. Server di 94 negara, unlimited bandwidth.', 30000, 'security'),
('Surfshark Premium', 'VPN unlimited device. CleanWeb, Camouflage Mode, dan MultiHop.', 22000, 'security');

-- OTAKU
INSERT IGNORE INTO products (name, description, price, category) VALUES
('Wibuku Premium', 'Baca komik & manga premium tanpa batas. Koleksi lengkap bahasa Indonesia.', 15000, 'otaku'),
('Wattpad Premium', 'Baca cerita tanpa iklan, offline reading, dan akses cerita premium.', 18000, 'otaku'),
('Serial+ Premium', 'Streaming anime, drama, dan serial premium tanpa iklan.', 20000, 'otaku');
