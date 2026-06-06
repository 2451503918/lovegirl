-- Migration v5.2.1: Add visitor_ips table for IP-based visitor deduplication
-- Requires UNIQUE index on (visit_date, ip) for INSERT IGNORE to work correctly

CREATE TABLE IF NOT EXISTS visitor_ips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_date DATE NOT NULL,
    ip VARCHAR(45) NOT NULL,
    UNIQUE KEY idx_visit_date_ip (visit_date, ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Performance Indexes for v5.2.1
-- ============================================

-- visitor_stats.visit_date: used in WHERE visit_date = ? queries
-- Critical for visitor tracking performance
CREATE INDEX IF NOT EXISTS idx_visitor_stats_date ON visitor_stats (visit_date);

-- timeline.date: used in ORDER BY date ASC/DESC queries
-- Critical for timeline page performance
CREATE INDEX IF NOT EXISTS idx_timeline_date ON timeline (date);

-- visitor_total: ensure id has index (should already be PK)
-- visitor_ips: already covered by UNIQUE KEY idx_visit_date_ip
