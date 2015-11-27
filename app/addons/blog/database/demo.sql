REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window, related_ids) VALUES ('7', '0', '7', 'A', 'B', '0', '1415336000', '0', NULL);

REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window, related_ids) VALUES ('40', '7', '7/40', 'A', 'B', '0', UNIX_TIMESTAMP()-639485, '0', NULL);
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window, related_ids) VALUES ('41', '7', '7/41', 'A', 'B', '0', UNIX_TIMESTAMP()-529384, '0', NULL);
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window, related_ids) VALUES ('42', '7', '7/42', 'A', 'B', '0', UNIX_TIMESTAMP()-458585, '0', NULL);
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window, related_ids) VALUES ('43', '7', '7/43', 'A', 'B', '0', UNIX_TIMESTAMP()-418474, '0', NULL);
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window, related_ids) VALUES ('44', '7', '7/44', 'A', 'B', '0', UNIX_TIMESTAMP()-373636, '0', NULL);
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window, related_ids) VALUES ('45', '7', '7/45', 'A', 'B', '0', UNIX_TIMESTAMP()-334434, '0', NULL);
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window, related_ids) VALUES ('46', '7', '7/46', 'A', 'B', '0', UNIX_TIMESTAMP()-232323, '0', NULL);
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window, related_ids) VALUES ('47', '7', '7/47', 'A', 'B', '0', UNIX_TIMESTAMP()-111111, '0', NULL);

REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (7, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (8, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (9, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (10, 1);

REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (40, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (41, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (42, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (43, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (44, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (45, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (46, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (47, 1);

REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`)
VALUES
  (1074, '1.png', 894, 305),
  (1073, '2.png', 894, 305),
  (1072, '3.png', 894, 305);


REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`)
VALUES
  (953, 8, 'blog', 1074, 0, 'M', 0),
  (952, 9, 'blog', 1073, 0, 'M', 0),
  (951, 10, 'blog', 1072, 0, 'M', 0);