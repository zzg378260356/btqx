import { defineComponent, h, type PropType, type VNode, type CSSProperties } from "vue";
import FigmaImage from "./FigmaImage.vue";
export interface DesignNode { tag: string; props: Record<string, unknown> & { style?: CSSProperties }; children: (DesignNode | string | number)[]; }
// Only coordinates whose containing block is the full design plane need remapping.
// Nested component geometry remains local, preserving the cached Figma assets.
function coordinate(value: unknown, size: number): number | undefined {
  if (typeof value !== 'string') return undefined;
  const expression = value.replace(/calc\(|\)/g, '').replace(/(-?[\d.]+)%/g, (_, n) => String(Number(n) * size / 100)).replace(/px/g, '');
  if (!/^[\d.\s+\-]+$/.test(expression)) return undefined;
  return expression.match(/[+-]?\s*[\d.]+/g)?.reduce((sum, part) => sum + Number(part.replace(/\s/g, '')), 0);
}
function fluidStyle(original: CSSProperties): CSSProperties {
  const style = { ...original };
  if (style.inset) {
    const values = String(style.inset).split(' ');
    if (values.length === 4) {
      [style.top, style.right, style.bottom, style.left] = values;
      delete style.inset;
    }
  }
  let left = coordinate(style.left, 1920);
  const right = coordinate(style.right, 1920);
  const width = coordinate(style.width, 1920) ?? (left !== undefined && right !== undefined ? 1920 - left - right : undefined);
  if (left === undefined && right !== undefined && width !== undefined) left = 1920 - right - width;
  if (left === undefined) return style;
  const factor = left >= 1504 ? 1 : left <= 412 ? 0 : (left - 436) / 1060;
  style.left = `calc(${left}px + var(--content-extra, 0px) * ${factor})`;
  if (width !== undefined) {
    style.width = left > 412 && left < 1504 ? `calc(${width}px + var(--content-extra, 0px) * ${width / 1060})` : `${width}px`;
    delete style.right;
  }
  return style;
}
function draw(node: DesignNode | string | number, global = true): VNode | string | number {
  if (typeof node !== "object") return node;
  const style = node.props.style;
  const props = { ...node.props, ...(global && style ? { style: fluidStyle(style) } : {}) };
  if (node.tag === "img") return h(FigmaImage, { ...props, src: String(node.props.src), alt: String(node.props.alt || "") });
  return h(node.tag, props, node.children.map(child => draw(child, global && (!style || style.display === 'contents'))));
}
export default defineComponent({
  props: { tree: { type: Object as PropType<DesignNode>, required: true }, fluid: { type: Boolean, default: false } },
  setup: props => () => draw(props.tree, props.fluid),
});
