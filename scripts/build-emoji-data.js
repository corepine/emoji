import fs from 'node:fs';
import path from 'node:path';

const sourcePath = path.resolve('node_modules/emojibase-data/en/data.json');
const outputPath = path.resolve('resources/data/en/emoji.json');

const categoryLabels = new Map([
  [0, ['smileys-people', 'Smileys & People', '☺']],
  [1, ['smileys-people', 'Smileys & People', '☺']],
  [3, ['animals-nature', 'Animals & Nature', '♧']],
  [4, ['food-drink', 'Food & Drink', '☕']],
  [5, ['travel-places', 'Travel & Places', '◉']],
  [6, ['activities', 'Activities', '⚽']],
  [7, ['objects', 'Objects', '✦']],
  [8, ['symbols', 'Symbols', '#']],
  [9, ['flags', 'Flags', '⚑']],
]);

if (!fs.existsSync(sourcePath)) {
  throw new Error(`Missing Emojibase data at ${sourcePath}. Run npm install first.`);
}

const data = JSON.parse(fs.readFileSync(sourcePath, 'utf8'));
const grouped = new Map();

for (const item of data) {
  if (!item.emoji || item.group === undefined) {
    continue;
  }

  const meta = categoryLabels.get(item.group);

  if (!meta) {
    continue;
  }

  const [key, label, icon] = meta;

  if (!grouped.has(key)) {
    grouped.set(key, {
      key,
      label,
      icon,
      emojis: [],
    });
  }

  grouped.get(key).emojis.push({
    emoji: item.emoji,
    label: item.label ?? item.annotation ?? item.name ?? item.emoji,
    tags: Array.from(new Set([
      ...(item.tags ?? []),
      ...(item.shortcodes ?? []),
    ])).slice(0, 12),
  });
}

const categories = Array.from(grouped.values()).filter((category) => category.emojis.length > 0);

fs.mkdirSync(path.dirname(outputPath), { recursive: true });
fs.writeFileSync(outputPath, `${JSON.stringify(categories)}\n`);

console.log(`Generated ${categories.reduce((total, category) => total + category.emojis.length, 0)} emojis in ${categories.length} categories.`);
