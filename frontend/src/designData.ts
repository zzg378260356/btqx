export interface Warning {
  id: number; title: string; level: string; district: string; status: string;
  published_at: string; responsible_unit: string; updated_at?: string; population?: number;
}
export interface DashboardData {
  counts: Record<string, number>;
  devices: { total: number; online: number };
  warnings: Warning[];
  trend: { dates: string[]; series: Record<string, number[]> };
  types: { name: string; value: number }[];
}
export const designData: DashboardData = {
  counts: { red: 12, orange: 18, yellow: 32, blue: 26 },
  devices: { total: 152, online: 152 },
  warnings: Array.from({ length: 7 }, (_, i) => ({
    id: i + 1, title: "土默特右旗-山洪风险预警", level: "red", district: "东河区",
    status: "处置中", published_at: "2026-09-30 14:30", responsible_unit: "应急管理局", population: 8560,
  })),
  trend: {
    dates: ["07-02", "07-03", "07-04", "07-05", "07-07", "07-08"],
    series: { red: [30, 25, 31, 34, 41, 40], orange: [23, 19, 24, 26, 35, 25], yellow: [15, 10, 16, 18, 28, 15], blue: [3, 2, 4, 7, 15, 4] },
  },
  types: [{ name: "暴雨", value: 36 }, { name: "雷电", value: 20 }, { name: "大风", value: 16 }, { name: "冰雹", value: 10 }, { name: "其他", value: 6 }],
};
export const designTracking: Warning[] = [
  { id: 101, title: "土默特右旗-山洪风险预警", level: "red", district: "土默特右旗", status: "处置中", responsible_unit: "应急管理局", published_at: "2026-07-08 14:33" },
  { id: 102, title: "全阳县-地质灾害风险预警", level: "orange", district: "土默特右旗", status: "处置中", responsible_unit: "自然资源局", published_at: "2026-07-08 14:33" },
  { id: 103, title: "石拐区-山洪风险预警", level: "yellow", district: "土默特右旗", status: "已完成", responsible_unit: "水务局", published_at: "2026-07-08 14:33" },
  { id: 104, title: "东河区-强降雨风险预警", level: "blue", district: "土默特右旗", status: "处置中", responsible_unit: "气象局", published_at: "2026-07-08 14:33" },
];
