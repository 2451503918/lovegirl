-- Migration v5.2.1: Add visitor_ips table for IP-based visitor deduplication
-- Requires UNIQUE index on (visit_date, ip) for INSERT IGNORE to work correctly

CREATE TABLE IF NOT EXISTS visitor_ips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_date DATE NOT NULL,
    ip VARCHAR(45) NOT NULL,
    UNIQUE KEY idx_visit_date_ip (visit_date, ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
