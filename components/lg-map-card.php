<?php
/**
 * 地图显示组件
 * 显示双方位置标记和距离
 */

// 如果可用，从数据库获取位置信息
$boyCity = '北京';
$girlCity = '上海';
$boyLat = 39.9042;
$boyLng = 116.4074;
$girlLat = 31.2304;
$girlLng = 121.4737;
$boyName = '男方';
$girlName = '女方';
$boyImg = '';
$girlImg = '';

// 尝试从全局$text变量获取（首页已加载）
if (isset($text) && is_array($text)) {
    $boyCity = $text['boyCity'] ?? $boyCity;
    $girlCity = $text['girlCity'] ?? $girlCity;
    $boyLat = floatval($text['boyLat'] ?? $boyLat);
    $boyLng = floatval($text['boyLng'] ?? $boyLng);
    $girlLat = floatval($text['girlLat'] ?? $girlLat);
    $girlLng = floatval($text['girlLng'] ?? $girlLng);
    $boyName = $text['boy'] ?? $boyName;
    $girlName = $text['girl'] ?? $girlName;
    $boyImg = $text['boyimg'] ?? '';
    $girlImg = $text['girlimg'] ?? '';
}

// 计算两点之间的距离（使用Haversine公式）
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // 地球半径（公里）
    
    $latDelta = deg2rad($lat2 - $lat1);
    $lonDelta = deg2rad($lon2 - $lon1);
    
    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($lonDelta / 2) * sin($lonDelta / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    return round($earthRadius * $c, 1);
}

$distance = calculateDistance($boyLat, $boyLng, $girlLat, $girlLng);

// 计算地图上标记的位置（按比例转换）
$mapScaleX = 400;
$mapScaleY = 200;
// 将经纬度转换为地图上的大致位置
$boyMapX = 100 + ($boyLng + 180) / 360 * $mapScaleX;
$boyMapY = 100 - ($boyLat + 90) / 180 * $mapScaleY;
$girlMapX = 100 + ($girlLng + 180) / 360 * $mapScaleX;
$girlMapY = 100 - ($girlLat + 90) / 180 * $mapScaleY;

// 头像URL
$boyAvatar = $boyImg ? "/services/avatar-proxy.php?type=qq&qq=" . urlencode($boyImg) . "&s=640" : "/services/avatar-generate.php?name=" . urlencode($boyName) . "&size=128&bg=667eea";
$girlAvatar = $girlImg ? "/services/avatar-proxy.php?type=qq&qq=" . urlencode($girlImg) . "&s=640" : "/services/avatar-generate.php?name=" . urlencode($girlName) . "&size=128&bg=f5576c";
?>

<!-- 地图显示组件 -->
<div class="lgnewui-map-card">
    <div class="lgnewui-map-header">
        <div class="lgnewui-map-title">
            <i class="fas fa-map-marked-alt"></i>
            <span>我们的距离</span>
        </div>
        <div class="lgnewui-map-distance">
            <span class="lgnewui-map-distance-value"><?php echo $distance; ?></span>
            <span class="lgnewui-map-distance-unit">km</span>
        </div>
    </div>
    
    <div class="lgnewui-map-visual">
        <!-- 地图背景 -->
        <div class="lgnewui-map-bg">
            <svg viewBox="0 0 400 200" class="lgnewui-map-svg">
                <!-- 中国地图轮廓（简化版） -->
                <path d="M100,50 Q150,30 200,50 Q250,40 300,60 L320,100 Q300,150 250,170 L200,180 Q150,170 100,150 L80,100 Z" 
                      fill="none" stroke="rgba(102, 126, 234, 0.2)" stroke-width="1"/>
                
                <!-- 双方位置标记 -->
                <g class="lgnewui-map-marker-boy">
                    <circle cx="<?php echo $boyMapX; ?>" cy="<?php echo $boyMapY; ?>" r="8" fill="#667eea" opacity="0.3">
                        <animate attributeName="r" values="8;12;8" dur="2s" repeatCount="indefinite"/>
                        <animate attributeName="opacity" values="0.3;0.1;0.3" dur="2s" repeatCount="indefinite"/>
                    </circle>
                    <circle cx="<?php echo $boyMapX; ?>" cy="<?php echo $boyMapY; ?>" r="4" fill="#667eea"/>
                    <text x="<?php echo $boyMapX; ?>" y="<?php echo $boyMapY - 15; ?>" text-anchor="middle" fill="#667eea" font-size="12" font-weight="bold">
                        <?php echo htmlspecialchars($boyName); ?>
                    </text>
                </g>
                
                <g class="lgnewui-map-marker-girl">
                    <circle cx="<?php echo $girlMapX; ?>" cy="<?php echo $girlMapY; ?>" r="8" fill="#f5576c" opacity="0.3">
                        <animate attributeName="r" values="8;12;8" dur="2s" repeatCount="indefinite" begin="0.5s"/>
                        <animate attributeName="opacity" values="0.3;0.1;0.3" dur="2s" repeatCount="indefinite" begin="0.5s"/>
                    </circle>
                    <circle cx="<?php echo $girlMapX; ?>" cy="<?php echo $girlMapY; ?>" r="4" fill="#f5576c"/>
                    <text x="<?php echo $girlMapX; ?>" y="<?php echo $girlMapY + 25; ?>" text-anchor="middle" fill="#f5576c" font-size="12" font-weight="bold">
                        <?php echo htmlspecialchars($girlName); ?>
                    </text>
                </g>
                
                <!-- 连接线 -->
                <line x1="<?php echo $boyMapX; ?>" y1="<?php echo $boyMapY; ?>" x2="<?php echo $girlMapX; ?>" y2="<?php echo $girlMapY; ?>" 
                      stroke="url(#distanceGradient)" stroke-width="2" stroke-dasharray="5,5">
                    <animate attributeName="stroke-dashoffset" from="0" to="10" dur="1s" repeatCount="indefinite"/>
                </line>
                
                <!-- 渐变定义 -->
                <defs>
                    <linearGradient id="distanceGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#667eea"/>
                        <stop offset="100%" style="stop-color:#f5576c"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
    </div>
    
    <div class="lgnewui-map-locations">
        <div class="lgnewui-map-location lgnewui-map-location-boy">
            <div class="lgnewui-map-location-avatar">
                <img src="<?php echo $boyAvatar; ?>" alt="">
            </div>
            <div class="lgnewui-map-location-info">
                <div class="lgnewui-map-location-name"><?php echo htmlspecialchars($boyName); ?></div>
                <div class="lgnewui-map-location-city">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo htmlspecialchars($boyCity); ?>
                </div>
            </div>
        </div>
        
        <div class="lgnewui-map-connector">
            <i class="fas fa-heart"></i>
        </div>
        
        <div class="lgnewui-map-location lgnewui-map-location-girl">
            <div class="lgnewui-map-location-avatar">
                <img src="<?php echo $girlAvatar; ?>" alt="">
            </div>
            <div class="lgnewui-map-location-info">
                <div class="lgnewui-map-location-name"><?php echo htmlspecialchars($girlName); ?></div>
                <div class="lgnewui-map-location-city">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo htmlspecialchars($girlCity); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* 地图卡片样式 */
.lgnewui-map-card {
    background: white;
    border-radius: 24px;
    padding: 24px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.lgnewui-map-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.lgnewui-map-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #333;
}

.lgnewui-map-title i {
    color: #667eea;
    font-size: 24px;
}

.lgnewui-map-distance {
    display: flex;
    align-items: baseline;
    gap: 4px;
}

.lgnewui-map-distance-value {
    font-size: 36px;
    font-weight: 900;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.lgnewui-map-distance-unit {
    font-size: 16px;
    color: #999;
    font-weight: 600;
}

.lgnewui-map-visual {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    overflow: hidden;
}

.lgnewui-map-bg {
    position: relative;
}

.lgnewui-map-svg {
    width: 100%;
    height: auto;
    display: block;
}

.lgnewui-map-locations {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.lgnewui-map-location {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 16px;
}

.lgnewui-map-location-boy {
    background: linear-gradient(135deg, rgba(102,126,234,0.1), rgba(118,75,162,0.1));
}

.lgnewui-map-location-girl {
    background: linear-gradient(135deg, rgba(245,87,108,0.1), rgba(240,147,251,0.1));
}

.lgnewui-map-location-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.lgnewui-map-location-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.lgnewui-map-location-info {
    flex: 1;
    min-width: 0;
}

.lgnewui-map-location-name {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 4px;
}

.lgnewui-map-location-city {
    font-size: 13px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 4px;
}

.lgnewui-map-location-city i {
    color: #667eea;
}

.lgnewui-map-connector {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #f5576c);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

/* 移动端适配 */
@media (max-width: 480px) {
    .lgnewui-map-card {
        padding: 16px;
        border-radius: 20px;
    }
    
    .lgnewui-map-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .lgnewui-map-distance-value {
        font-size: 28px;
    }
    
    .lgnewui-map-locations {
        flex-direction: column;
        gap: 12px;
    }
    
    .lgnewui-map-connector {
        transform: rotate(90deg);
    }
    
    .lgnewui-map-location {
        width: 100%;
    }
}
</style>
