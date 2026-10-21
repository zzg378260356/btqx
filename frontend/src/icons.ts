export const icons = {
  rain: '/icon/87cc4a1a-3f4d-447b-9632-6c869ad0dd26.png',
  hexagon: '/icon/5fb652c3-961f-47e5-995c-646fb4e7b9aa.png',
  hail: '/icon/c6d6a2c7-c0e1-45a8-8b1e-3a6ab40231d4.png',
  lightning: '/icon/0af5ad1c-5d09-410f-ad73-1ecccc9c87e9.png',
  fullscreen: '/icon/e77fd81f-1968-4224-8ffe-b7dc9b9b5ad3.png',
  wind: '/icon/2a0e0471-4142-4f59-ac84-2ac0db6acfff.png',
  thunder: '/icon/f88f4195-0400-4ef0-a818-ddecca66ee4c.png',
  orangeWarning: '/icon/68f308d6-b750-40ce-b822-c06a5675e676.png',
  deviceOnline: '/icon/68c9a9b9-89ef-43c5-9f43-e9c14a25e385.png',
  notification: '/icon/936de0e1-9d34-4adb-905c-ebbb4baa8170.png',
};

// Swap only the exported metric bases; preserve the remaining original layers.
export const designImageOverrides: Record<string, string> = Object.fromEntries([
  '66833337-4ae5-4c33-b5b9-370ca93316a9',
  '298ad753-7ede-4e93-bfe4-fa8ecc7ce65c',
  '580b9368-abe0-4cf9-b905-9f296377c252',
].map(id => [`/figma/overview/${id}-03e85.png`, icons.hexagon]));
