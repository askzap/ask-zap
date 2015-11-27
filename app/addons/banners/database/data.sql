REPLACE INTO ?:banners (`banner_id`, `status`, `type`, `target`, `localization`, `timestamp`) VALUES(6, 'A', 'G', 'T', '', UNIX_TIMESTAMP(NOW()));
REPLACE INTO ?:banners (`banner_id`, `status`, `type`, `target`, `localization`, `timestamp`) VALUES(7, 'A', 'G', 'T', '', 1421096400);
REPLACE INTO ?:banners (`banner_id`, `status`, `type`, `target`, `localization`, `timestamp`) VALUES(8, 'A', 'G', 'T', '', 1418072400);
REPLACE INTO ?:banners (`banner_id`, `status`, `type`, `target`, `localization`, `timestamp`) VALUES(9, 'A', 'G', 'T', '', 1418072400);

REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(189, 'common_image_1.jpg', 171, 149);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(190, 'common_image_2.gif', 171, 170);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(233, 'banner_1.jpg', 940, 400);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(234, 'banner_2.jpg', 940, 400);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(235, 'banner_3.jpg', 940, 400);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1175, 'nokian1.png', 743, 407);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1176, 'gift_certificate.png', 1200, 136);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1177, 'holiday_gift.png', 900, 175);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1186, 'shop_with_easy.png', 433, 407);

REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(136, 1, 'promo', 189, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(137, 2, 'promo', 190, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(177, 3, 'promo', 233, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(178, 4, 'promo', 234, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(179, 5, 'promo', 235, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(1059, 14, 'promo', 1175, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(1062, 17, 'promo', 1176, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(1064, 19, 'promo', 1177, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(1076, 22, 'promo', 1186, 0, 'M', 0);


REPLACE INTO ?:banners (banner_id, status, type, target, timestamp, position) VALUES(16,  'A', 'G', 'T', UNIX_TIMESTAMP() - 10000, 40);
REPLACE INTO ?:banners (banner_id, status, type, target, timestamp, position) VALUES(17,  'A', 'G', 'T', UNIX_TIMESTAMP() - 5000, 30);
REPLACE INTO ?:banners (banner_id, status, type, target, timestamp, position) VALUES(18,  'A', 'G', 'T', UNIX_TIMESTAMP() - 2000, 20);
REPLACE INTO ?:banners (banner_id, status, type, target, timestamp, position) VALUES(19,  'A', 'G', 'T', UNIX_TIMESTAMP() - 1000, 10);
REPLACE INTO ?:banners (banner_id, status, type, target, timestamp, position) VALUES(20, 'A', 'G', 'T', UNIX_TIMESTAMP() - 500, 0);
REPLACE INTO ?:banners (banner_id, status, type, target, timestamp, position) VALUES(21, 'A', 'G', 'T', UNIX_TIMESTAMP() - 300, 0);

REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1375, 'banner-sale-40-80.png', 740, 395);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1374, 'banner-xbox360.png', 740, 395);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1373, 'banner-point.png', 740, 395);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1372, 'banner-girl.png', 740, 395);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1360, 'banner_free_ship_lies-pz.png', 434, 185);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1361, 'banner_pickup_ok56-7h.png', 434, 185);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1300, 'banner-en-sale-40-80.png', 740, 395);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1301, 'banner-en-xbox360.jpg', 740, 395);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1302, 'banner-en-point.jpg', 740, 395);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1303, 'banner-en-girl.jpg', 740, 395);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1304, 'banner_en_free_ship_lies-pz.png', 434, 185);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1305, 'banner_en_pickup_ok56-7h.png', 434, 185);

REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(31, 'promo', 1375, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(29, 'promo', 1374, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(27, 'promo', 1373, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(25, 'promo', 1372, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(32, 'promo', 1360, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(33, 'promo', 1361, 0, 'M', 0);

REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(35, 'promo', 1300, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(36, 'promo', 1301, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(37, 'promo', 1302, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(38, 'promo', 1303, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(39, 'promo', 1304, 0, 'M', 0);
REPLACE INTO ?:images_links (`object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(40, 'promo', 1305, 0, 'M', 0);
