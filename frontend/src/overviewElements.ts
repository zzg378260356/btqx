export interface RiskGroup { name: string; count: number; color: string; }
export interface WarningType { name: string; count: number; }
export interface WeatherReading {
  time: string; rainfall: number; windSpeed: number; temperature: number;
}
export interface OverviewElementsData {
  risks: RiskGroup[];
  warningTypes: WarningType[];
  weather: WeatherReading[];
  windDirection: string;
  broadcasts: string[];
}
// Replace these records with API results; all labels, charts and details derive from them.
export const mockOverviewElements: OverviewElementsData = {
  risks: [
    { name: '高风险', count: 3, color: '#ff4168' },
    { name: '较高风险', count: 5, color: '#ffab54' },
    { name: '中风险', count: 9, color: '#f5d64e' },
    { name: '较低风险', count: 10, color: '#36d9be' },
    { name: '低风险', count: 6, color: '#3aaaff' },
  ],
  warningTypes: [{ name: '暴雨', count: 36 }, { name: '雷电', count: 21 }, { name: '大风', count: 16 }, { name: '冰雹', count: 10 }, { name: '高温', count: 7 }],
  weather: Array.from({ length: 8 }, (_, i) => ({
    time: `2026-10-20 ${String(6 + i).padStart(2, '0')}:00`,
    rainfall: [0, 1.2, 2.4, 4.6, 6.8, 5.2, 3.8, 2.4][i]!,
    windSpeed: [2.1, 2.6, 3.2, 4.1, 3.6, 2.9, 3.1, 2.8][i]!,
    temperature: [6.2, 7.4, 9.1, 11.3, 13.2, 14.6, 15.1, 14.8][i]!,
  })),
  windDirection: '东南风',
  broadcasts: [
    '【演示】包头市气象台发布暴雨橙色预警：东河区未来3小时局地降水较强，请注意山洪及地质灾害风险。',
    '【演示】青山区发布雷电蓝色预警：未来6小时可能发生雷电活动，请减少户外作业。',
    '【演示】昆都仑区发布大风蓝色预警：局地阵风可达7级，请加固临时设施。',
    '【演示】石拐区重点隐患点进入巡查状态，请相关责任人保持通信畅通。',
  ],
};
