# 包头市重点区域气象灾害监测预警平台

Laravel 13 + Filament 5 管理后台，Vue 3 + TypeScript + ECharts + Cesium 综合态势大屏。项目根目录分为 `backend/`、`frontend/`、`docker/` 和 `scripts/`。

## 首次启动

需要 Docker Compose。进入项目根目录运行：

```bash
./scripts/setup.sh
docker compose exec backend php artisan btqx:create-admin admin@example.com
```

管理员密码通过隐藏提示输入，至少 12 位。不会初始化固定密码。首次运行使用 SQLite，演示数据仅在 local/testing 环境生成，重复初始化不会重复插入。

- 大屏：http://localhost:5173
- 管理后台：http://localhost:5173/admin
- 数据接口：http://localhost:8000/api/dashboard

Compose 默认绑定本机地址，是开发环境配置。正式部署需要反向代理、HTTPS，并设置 `APP_ENV=production`、`APP_DEBUG=false` 和实际 `APP_URL`。

## 本机开发

后端要求 PHP 8.4+、Composer 2；前端要求 Node.js 20.19+（推荐 22）。

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan btqx:create-admin admin@example.com
php artisan serve
```

另开终端：

```bash
cd frontend
npm ci
npm run dev
```

Vite 将 `/api`、`/admin` 和 Filament/Livewire 资源代理到后端，默认地址 `http://127.0.0.1:8000`，可通过 `API_PROXY_TARGET` 修改。

## 当前功能

- 综合态势首页：有效预警统计、七日趋势、灾害占比、预警筛选/详情和处置跟踪；每 30 秒读取数据库。
- Filament 管理后台：管理员登录、设备和预警新增/编辑/筛选、监测概览。
- 设备字段包含编号、类型、区域、坐标、运行状态和最后上报时间；坐标与唯一编号有校验。
- Cesium 离线椭球与程序生成建筑/监测点，可旋转缩放，不使用 ion Token。浏览器缺少 WebGL 时显示明确标注的示意图。

**演示数据说明：** 当前天气、渠道统计、建筑和监测点为示例，设备状态由后台维护，尚未接入真实气象接口、设备心跳或 MQTT。其他顶部模块标记为待建设。真实地形、影像、3D Tiles 需要另行接入。

## 验证

```bash
cd frontend && npm run build
cd ../backend && php artisan test --compact
```

或者 `docker compose exec backend php artisan test --compact`。测试覆盖数据统计、后台访问控制、设备创建/坐标校验和预警创建到大屏的联动。

## 后续数据库与部署

后端已保留 Laravel PostgreSQL 配置，Docker 镜像安装了 `pdo_pgsql`。切换时配置 `.env` 的 `DB_CONNECTION=pgsql` 和连接参数，再执行迁移；PostGIS、Redis、Reverb 与设备通信均未在本次初始化中启用。

生产大屏执行 `npm run build` 后部署 `frontend/dist/`；反向代理 `/api`、`/admin`、`/livewire`、`/css/filament`、`/js/filament`、`/fonts/filament` 到 Laravel。Laravel 公共根目录必须为 `backend/public`。Cesium 静态资源随构建复制至 `/cesium`。
