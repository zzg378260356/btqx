<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref, shallowRef } from "vue";
import Chart from "./components/Chart.vue";
import Scene from "./components/Scene.vue";
import type { EChartsOption } from "echarts";
interface Warning {
  id: number;
  title: string;
  level: string;
  district: string;
  status: string;
  published_at: string;
  responsible_unit: string;
}
const warnings = ref<Warning[]>([]);
const counts = ref<Record<string, number>>({
  red: 0,
  orange: 0,
  yellow: 0,
  blue: 0,
});
const deviceCount = ref(0);
const onlineCount = ref(0);
const level = ref("");
const district = ref("");
const selected = ref<Warning | null>(null);
const station = ref("");
const error = ref("");
const clock = ref(new Date());
const timer = setInterval(() => (clock.value = new Date()), 1000);
let poll: ReturnType<typeof setInterval>;
const colors: Record<string, string> = {
  red: "#ff5574",
  orange: "#ffad47",
  yellow: "#e3e967",
  blue: "#37cfff",
};
const names: Record<string, string> = {
  red: "红色",
  orange: "橙色",
  yellow: "黄色",
  blue: "蓝色",
};
const filtered = computed(() =>
  warnings.value.filter(
    (w) =>
      (!level.value || w.level === level.value) &&
      (!district.value || w.district === district.value),
  ),
);
const trend = shallowRef<EChartsOption>({});
const pie = shallowRef<EChartsOption>({});
async function load() {
  try {
    const response = await fetch("/api/dashboard");
    if (!response.ok) throw new Error("数据服务暂不可用");
    const data = await response.json();
    warnings.value = data.warnings;
    counts.value = data.counts;
    deviceCount.value = data.devices.total;
    onlineCount.value = data.devices.online;
    trend.value = {
      color: Object.values(colors),
      tooltip: { trigger: "axis" },
      legend: { textStyle: { color: "#99bdd5" }, itemWidth: 8, itemHeight: 8 },
      grid: { left: 32, right: 12, bottom: 30, top: 40 },
      xAxis: {
        type: "category",
        data: data.trend.dates,
        axisLabel: { color: "#99bdd5" },
        axisLine: { lineStyle: { color: "#26445e" } },
      },
      yAxis: {
        type: "value",
        axisLabel: { color: "#99bdd5" },
        splitLine: { lineStyle: { color: "#16334b" } },
      },
      series: Object.keys(names).map((k) => ({
        name: names[k],
        type: "line",
        smooth: true,
        symbolSize: 5,
        data: data.trend.series[k],
      })),
    };
    pie.value = {
      color: ["#37cfff", "#8d7cf4", "#e3e967", "#ff5574", "#ffad47"],
      tooltip: { trigger: "item" },
      legend: {
        orient: "vertical",
        right: 0,
        top: "center",
        textStyle: { color: "#afc9df" },
        itemWidth: 8,
      },
      series: [
        {
          type: "pie",
          radius: ["52%", "70%"],
          center: ["30%", "50%"],
          label: { show: false },
          data: data.types,
        },
      ],
      graphic: {
        type: "text",
        left: "20%",
        top: "43%",
        style: {
          text: String(data.warnings.length) + "\n预警总数",
          fill: "#d8f3ff",
          fontSize: 18,
          align: "center",
        },
      },
    };
    error.value = "";
  } catch (e) {
    error.value = e instanceof Error ? e.message : "数据读取失败";
  }
}
onMounted(() => {
  load();
  poll = setInterval(load, 30000);
});
onBeforeUnmount(() => {
  clearInterval(timer);
  clearInterval(poll);
});
const tabs = [
  "综合态势",
  "三维孪生一张图",
  "监测设备中心",
  "检测点详情",
  "预警中心",
  "应急处置指挥",
  "智能模型分析",
  "数据资源中心",
  "事态复盘评估",
];
const active = ref("综合态势");
function navigate(tab: string) {
  if (tab === "监测设备中心") {
    window.open("/admin/devices", "_blank");
    return;
  }
  active.value = tab;
}
</script>
<template>
  <div class="dashboard">
    <header>
      <div class="weather">
        <b>{{ clock.toLocaleTimeString("zh-CN", { hour12: false }) }}</b
        ><small>{{ clock.toLocaleDateString("zh-CN") }}</small
        ><span>☀ 晴　23° <em>演示</em></span>
      </div>
      <h1>包头市重点区域气象灾害监测预警平台</h1>
      <a href="/admin" target="_blank">◉ 管理后台 ↗</a>
    </header>
    <nav>
      <button
        v-for="tab in tabs"
        :key="tab"
        :class="{ active: active === tab }"
        @click="navigate(tab)"
      >
        {{ tab }}
      </button>
    </nav>
    <div class="status-bar">
      <span><i></i> {{ error || "数据服务已连接 · 每 30 秒更新" }}</span
      ><span>演示环境 · 数据与建筑模型仅用于功能展示</span>
    </div>
    <div
      v-if="!['综合态势', '三维孪生一张图'].includes(active)"
      class="module-notice"
    >
      {{ active }}模块将在后续建设，当前展示综合态势首页。<button
        @click="active = '综合态势'"
      >
        返回综合态势
      </button>
    </div>
    <main>
      <aside class="left-column">
        <section class="panel">
          <h2>预警统计 <small>实时概览</small></h2>
          <div class="warning-counts">
            <button
              v-for="(name, key) in names"
              :key="key"
              :style="{ '--level-color': colors[key] }"
              @click="level = level === key ? '' : String(key)"
            >
              <span class="beacon">♟</span
              ><strong>{{ counts[key] }}<small> 条</small></strong
              ><span>{{ name }}预警</span>
            </button>
          </div>
        </section>
        <section class="panel trend">
          <h2>预警趋势 <small>最近 7 天</small></h2>
          <Chart :option="trend" />
        </section>
        <section class="panel type">
          <h2>预警类型占比</h2>
          <Chart :option="pie" />
        </section>
      </aside>
      <div class="center-column">
        <div class="metrics">
          <article>
            <span>◉</span>
            <div>
              <small>当前有效预警</small
              ><strong>{{ warnings.length }}<em> 条</em></strong>
            </div>
          </article>
          <article>
            <span>⌁</span>
            <div>
              <small>监测设备总数</small
              ><strong>{{ deviceCount }}<em> 台</em></strong>
            </div>
          </article>
          <article>
            <span>♧</span>
            <div>
              <small>在线监测设备</small
              ><strong>{{ onlineCount }}<em> 台</em></strong>
            </div>
          </article>
          <article>
            <span>◎</span>
            <div>
              <small>设备在线率</small
              ><strong
                >{{
                  deviceCount
                    ? Math.round((onlineCount / deviceCount) * 100)
                    : 0
                }}<em> %</em></strong
              >
            </div>
          </article>
        </div>
        <div class="map-panel">
          <Scene @select="station = $event" />
          <div class="map-title">
            重点区域 · 三维监测态势<small
              >BAOTOU METEOROLOGICAL MONITORING</small
            >
          </div>
          <div class="legend">
            <b>预警释义</b
            ><span v-for="(name, key) in names" :key="key"
              ><i :style="{ background: colors[key] }"></i>{{ name }}预警</span
            >
          </div>
          <div v-if="station" class="station-info">
            {{ station }} <button @click="station = ''">×</button
            ><small>演示监测点 · 点击后台查看设备档案</small>
          </div>
        </div>
        <section class="panel tracking">
          <h2>
            预警处置跟踪 <small>{{ filtered.length }} 条记录</small>
          </h2>
          <div class="table-scroll">
            <table>
              <thead>
                <tr>
                  <th>预警名称</th>
                  <th>预警等级</th>
                  <th>发布时间</th>
                  <th>影响区域</th>
                  <th>处置状态</th>
                  <th>责任单位</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="w in filtered" :key="w.id" @click="selected = w">
                  <td>{{ w.title }}</td>
                  <td>
                    <span
                      class="badge"
                      :style="{
                        color: colors[w.level],
                        borderColor: colors[w.level],
                      }"
                      >{{ names[w.level] }}预警</span
                    >
                  </td>
                  <td>{{ w.published_at }}</td>
                  <td>{{ w.district }}</td>
                  <td>
                    <span
                      :class="['state', w.status === '已完成' ? 'done' : '']"
                      >{{ w.status }}</span
                    >
                  </td>
                  <td>{{ w.responsible_unit }}</td>
                </tr>
                <tr v-if="!filtered.length">
                  <td colspan="6">{{ error || "暂无符合条件的预警" }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
      <aside class="right-column">
        <section class="panel warning-list">
          <h2>
            预警信息列表 <small>{{ filtered.length }} 条</small>
          </h2>
          <div class="filters">
            <select v-model="level">
              <option value="">全部等级</option>
              <option v-for="(name, key) in names" :key="key" :value="key">
                {{ name }}预警
              </option></select
            ><select v-model="district">
              <option value="">全部区域</option>
              <option
                v-for="d in [...new Set(warnings.map((w) => w.district))]"
                :key="d"
              >
                {{ d }}
              </option>
            </select>
          </div>
          <div class="list-scroll">
            <button
              class="warning-item"
              v-for="w in filtered"
              :key="w.id"
              @click="selected = w"
            >
              <span
                class="warning-icon"
                :style="{
                  color: colors[w.level],
                  borderColor: colors[w.level],
                }"
                >预<br />警</span
              >
              <div>
                <b>{{ w.title }}</b
                ><small>{{ w.published_at }} · {{ w.district }}</small>
              </div>
              <span>›</span>
            </button>
            <p v-if="!filtered.length" class="empty">暂无预警</p>
          </div>
        </section>
        <section class="panel channels">
          <h2>预警发布渠道</h2>
          <div class="channel-grid">
            <article
              v-for="c in [
                { name: '短信', value: '26,830', rate: '98.6' },
                { name: '微信', value: '18,450', rate: '97.3' },
                { name: '应急广播', value: '562', rate: '99.1' },
                { name: '手机 APP', value: '15,720', rate: '98.6' },
                { name: '电子屏', value: '236', rate: '97.3' },
                { name: '传真', value: '85', rate: '96.1' },
              ]"
              :key="c.name"
            >
              <span>◎ {{ c.name }}</span
              ><strong>{{ c.value }}<small> 条</small></strong
              ><small>成功率 {{ c.rate }}%</small>
            </article>
          </div>
          <p class="demo-label">渠道统计为演示数据</p>
        </section>
      </aside>
    </main>
    <footer>
      包头市气象灾害监测预警平台 · 综合态势 V0.1
      <span>气象感知 / 风险研判 / 协同处置</span>
    </footer>
    <div v-if="selected" class="modal-backdrop" @click.self="selected = null">
      <section class="modal">
        <h2>预警详情<button @click="selected = null">×</button></h2>
        <h3>{{ selected.title }}</h3>
        <p :style="{ color: colors[selected.level] }">
          {{ names[selected.level] }}预警 · {{ selected.status }}
        </p>
        <p>影响区域：{{ selected.district }}</p>
        <p>发布时间：{{ selected.published_at }}</p>
        <p>责任单位：{{ selected.responsible_unit }}</p>
        <small>当前为演示预警，实际处置请依据正式发布信息。</small>
      </section>
    </div>
  </div>
</template>
