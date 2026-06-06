#!/bin/bash
# 部署脚本 - 在服务器上执行

# 配置
DEPLOY_DIR="/www/wwwroot/lovedemo.54oimx.top"
GIT_REPO="https://github.com/2451503918/lovegirl.git"

echo "=== 开始部署 ==="
echo "目标目录: $DEPLOY_DIR"

# 检查目录是否存在
if [ -d "$DEPLOY_DIR" ]; then
    echo "目录已存在，检查是否是 git 仓库..."
    cd "$DEPLOY_DIR"
    
    if [ -d ".git" ]; then
        echo "正在拉取最新代码..."
        git fetch origin
        git reset --hard origin/main
        git pull origin main
    else
        echo "目录不是 git 仓库，重新克隆..."
        cd ..
        rm -rf lovedemo.54oimx.top
        git clone "$GIT_REPO" lovedemo.54oimx.top
    fi
else
    echo "目录不存在，克隆仓库..."
    cd /www/wwwroot
    git clone "$GIT_REPO" lovedemo.54oimx.top
fi

echo "=== 部署完成 ==="
