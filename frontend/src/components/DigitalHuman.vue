<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
const emit = defineEmits<{ close: [] }>();
const video = ref<HTMLVideoElement>();
const state = ref('未连接');
const error = ref('');
const text = ref('');
const connected = ref(false);
const busy = ref(false);
const sending = ref(false);
let pc: RTCPeerConnection | null = null;
let sessionid: string | number | null = null;
let controller: AbortController | null = null;
let opener: HTMLElement | null = null;
const closeButton = ref<HTMLButtonElement>();
async function api(path: string, body: object, signal?: AbortSignal) {
  const response = await fetch('/digital-human-api/' + path, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body), signal: signal || AbortSignal.timeout(15000) });
  if (!response.ok) throw new Error('数字人服务请求失败（' + response.status + '）');
  const result = await response.json();
  if (result.code !== undefined && result.code !== 0) throw new Error(result.msg || result.message || '数字人服务暂不可用');
  return result;
}
function disconnect() {
  controller?.abort(); controller = null;
  const previous = pc; pc = null;
  previous?.getReceivers().forEach(receiver => receiver.track?.stop());
  previous?.close();
  if (video.value) video.value.srcObject = null;
  sessionid = null; connected.value = false; busy.value = false; state.value = '未连接';
}
async function connect() {
  disconnect(); error.value = ''; busy.value = true; state.value = '正在连接…';
  if (!window.RTCPeerConnection) { error.value = '当前浏览器不支持数字人视频连接'; busy.value = false; return; }
  const current = new RTCPeerConnection(); pc = current;
  const abort = new AbortController(); controller = abort;
  const timeout = window.setTimeout(() => abort.abort(), 45000);
  abort.signal.addEventListener('abort', () => current.close(), { once: true });
  const stream = new MediaStream();
  current.ontrack = event => {
    stream.addTrack(event.track);
    if (video.value) { video.value.srcObject = stream; video.value.play().catch(() => { error.value = '请点击视频播放按钮启用声音'; }); }
  };
  current.onconnectionstatechange = () => {
    if (pc !== current) return;
    if (current.connectionState === 'connected') { connected.value = true; busy.value = false; state.value = '已连接'; window.clearTimeout(timeout); }
    if (['failed', 'disconnected'].includes(current.connectionState)) { disconnect(); error.value = '视频连接中断，请重新连接；远程访问需确保服务器 WebRTC 网络可达'; }
  };
  try {
    current.addTransceiver('video', { direction: 'recvonly' });
    current.addTransceiver('audio', { direction: 'recvonly' });
    await current.setLocalDescription(await current.createOffer());
    await new Promise<void>((resolve, reject) => {
      if (abort.signal.aborted) return reject(new Error('连接超时，请重试'));
      if (current.iceGatheringState === 'complete') return resolve();
      current.addEventListener('icegatheringstatechange', () => { if (current.iceGatheringState === 'complete') resolve(); });
      abort.signal.addEventListener('abort', () => reject(new Error('连接超时，请重试')), { once: true });
    });
    const answer = await api('offer', { sdp: current.localDescription!.sdp, type: current.localDescription!.type }, abort.signal);
    if (pc !== current) return;
    sessionid = answer.sessionid;
    await current.setRemoteDescription({ sdp: answer.sdp, type: answer.type });
    await new Promise<void>((resolve, reject) => {
      if (current.connectionState === 'connected') return resolve();
      current.addEventListener('connectionstatechange', () => { if (current.connectionState === 'connected') resolve(); });
      abort.signal.addEventListener('abort', () => reject(new Error('连接超时，请检查服务器视频网络')), { once: true });
    });
  } catch (e) {
    if (pc === current) { disconnect(); error.value = e instanceof Error ? e.message : '连接失败，请重试'; }
  } finally { window.clearTimeout(timeout); }
}
async function send(interrupt = false) {
  if (!connected.value || sessionid === null || sending.value) return;
  if (!interrupt && !text.value.trim()) return;
  sending.value = true; error.value = '';
  try { await api(interrupt ? 'interrupt_talk' : 'human', interrupt ? { sessionid } : { sessionid, text: text.value.trim(), type: 'echo', interrupt: true }); }
  catch (e) { error.value = e instanceof Error ? e.message : '请求失败'; }
  finally { sending.value = false; }
}
function keydown(event: KeyboardEvent) {
  if (event.key === 'Escape') { event.stopPropagation(); emit('close'); }
  if (event.key === 'Tab') {
    const controls = Array.from(document.querySelectorAll<HTMLElement>('.digital-human-dialog button:not(:disabled), .digital-human-dialog textarea'));
    const first = controls[0], last = controls[controls.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
  }
}
onMounted(() => { opener = document.activeElement as HTMLElement; closeButton.value?.focus(); });
onBeforeUnmount(() => { disconnect(); opener?.focus(); });
</script>
<template>
  <div class="digital-human-backdrop" @click.self="emit('close')" @keydown="keydown">
    <section class="digital-human-dialog" role="dialog" aria-modal="true" aria-labelledby="digital-human-title">
      <header><div><h2 id="digital-human-title">数字人</h2><span role="status">{{ state }}</span></div><button ref="closeButton" aria-label="关闭数字人" @click="emit('close')">×</button></header>
      <video ref="video" autoplay playsinline controls aria-label="数字人音视频"></video>
      <p v-if="error" role="alert">{{ error }}</p>
      <div class="digital-human-actions"><button :disabled="busy || connected" @click="connect">{{ busy ? '连接中…' : '连接数字人' }}</button><button :disabled="!busy && !connected" @click="disconnect">断开连接</button></div>
      <label for="digital-human-text">播报内容</label>
      <textarea id="digital-human-text" v-model="text" maxlength="2000" rows="3" placeholder="请输入需要数字人播报的内容" @keydown.ctrl.enter.prevent="send()"></textarea>
      <div class="digital-human-actions"><button :disabled="!connected || sending || !text.trim()" @click="send()">发送播报</button><button :disabled="!connected || sending" @click="send(true)">打断播报</button></div>
    </section>
  </div>
</template>
<style scoped>
.digital-human-backdrop { position: fixed; inset: 0; z-index: 1000; display: grid; place-items: center; background: #001020bb; padding: 16px; }
.digital-human-dialog { width: min(720px, 100%); max-height: 95vh; overflow: auto; padding: 24px; border: 1px solid #2587b4; border-radius: 12px; color: #e0ecff; background: #0b2339; box-shadow: 0 20px 80px #0009; font-size: 16px; }
header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; } h2 { margin: 0 0 6px; } header span { color: #77cfff; }
video { display: block; width: 100%; height: min(42vh, 360px); background: #03121e; border-radius: 8px; object-fit: contain; }
button { padding: 10px 18px; border: 1px solid #2587b4; border-radius: 6px; background: #123c59; color: #e0ecff; cursor: pointer; font-size: 16px; } button:disabled { opacity: .45; cursor: default; } button:focus-visible, textarea:focus-visible { outline: 2px solid #88dcff; outline-offset: 2px; }
.digital-human-actions { display: flex; gap: 12px; margin: 16px 0; } label { display: block; margin: 12px 0; } textarea { width: 100%; box-sizing: border-box; padding: 12px; background: #061c2e; color: #e0ecff; border: 1px solid #2587b4; border-radius: 6px; resize: vertical; font: inherit; } p { color: #ffbd98; }
</style>
