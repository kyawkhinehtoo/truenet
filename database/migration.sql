insert into migrations VALUES (null,'0001_01_01_000000_create_users_table',1);
insert into migrations VALUES (null,'0001_01_01_000002_create_jobs_table',1);
insert into migrations VALUES (null,'2025_01_06_140834_add_two_factor_columns_to_users_table',1);
insert into migrations VALUES (null,'2025_01_06_140843_create_personal_access_tokens_table',1);
insert into migrations VALUES (null,'2025_01_06_144149_create_activity_log_table',1);
insert into migrations VALUES (null,'2025_01_06_144150_add_event_column_to_activity_log_table',1);
insert into migrations VALUES (null,'2025_01_06_144151_add_batch_uuid_column_to_activity_log_table',1);
insert into migrations VALUES (null,'2025_01_07_040953_add_role_into_user',1);


-- 18 January 2025 10:01:21 PM
ALTER TABLE `dk_imdb`.`customers`
CHANGE `pop_device_id` `pop_device_id` bigint unsigned NULL;

ALTER TABLE customers
ADD CONSTRAINT fk_sn_id FOREIGN KEY (sn_id)
REFERENCES sn_ports(id)
ON DELETE SET NULL;

ALTER TABLE customers MODIFY COLUMN pop_id BIGINT UNSIGNED NULL;
ALTER TABLE customers
ADD CONSTRAINT fk_pop_id FOREIGN KEY (pop_id)
REFERENCES pops(id)
ON DELETE SET NULL;

UPDATE customers
SET pop_device_id = NULL
WHERE pop_device_id = 0
AND pop_device_id NOT IN (SELECT id FROM pop_devices);

ALTER TABLE customers
ADD CONSTRAINT fk_pop_device_id FOREIGN KEY (pop_device_id)
REFERENCES pop_devices(id)
ON DELETE SET NULL;

UPDATE customers AS c
JOIN sn_ports AS s      ON c.sn_id = s.id
JOIN dn_ports AS d      ON s.dn_id = d.id
JOIN pop_devices AS pd  ON d.pop_device_id = pd.id
JOIN pops AS p          ON pd.pop_id = p.id
SET
    c.pop_id         = p.id,
    c.pop_device_id  = pd.id,
    c.dn_id          = d.id

ALTER TABLE `customer_histories`
CHANGE `general` `general` blob NULL;

ALTER TABLE `dk_imdb`.`email_templates`
DROP COLUMN `default`;

ALTER TABLE `dk_imdb`.`short_urls`
ADD COLUMN `forward_query_params` varchar(255) NULL AFTER `deactivated_at`;