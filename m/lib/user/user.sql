
-- template for the user table.

CREATE TABLE m_users (
	u_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	u_name VARCHAR(16),
	u_email VARCHAR(64),
	u_phash VARCHAR(128),
	u_psand VARCHAR(128)
);
