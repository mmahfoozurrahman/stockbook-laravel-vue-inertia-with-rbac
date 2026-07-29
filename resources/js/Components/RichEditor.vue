<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Highlight from '@tiptap/extension-highlight';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import { watch, onBeforeUnmount } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Write something thoughtful…' },
});
const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Highlight,
        Placeholder.configure({ placeholder: props.placeholder }),
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
    ],
    onUpdate: ({ editor }) => emit('update:modelValue', editor.getHTML()),
});

watch(() => props.modelValue, (value) => {
    if (editor.value && editor.value.getHTML() !== value) {
        editor.value.commands.setContent(value || '', false);
    }
});

onBeforeUnmount(() => editor.value?.destroy());

const tools = [
    ['bi-type-bold', 'bold'],
    ['bi-type-italic', 'italic'],
    ['bi-type-strikethrough', 'strike'],
    ['bi-highlighter', 'highlight'],
    ['bi-list-ul', 'bulletList'],
    ['bi-list-ol', 'orderedList'],
    ['bi-code-square', 'codeBlock'],
    ['bi-quote', 'blockquote'],
    ['bi-arrow-counterclockwise', 'undo'],
    ['bi-arrow-clockwise', 'redo'],
];

const run = (command) => {
    const toggle = `toggle${command[0].toUpperCase()}${command.slice(1)}`;
    return editor.value?.chain().focus()[toggle]?.().run()
        ?? editor.value?.chain().focus()[command]?.().run();
};
</script>

<template>
    <div class="rich-editor" :class="{ focused: editor?.isFocused }">
        <div class="editor-toolbar">
            <button
                v-for="[icon, command] in tools"
                :key="command"
                type="button"
                :class="{ active: editor?.isActive(command) }"
                :title="command"
                @click="run(command)"
            >
                <i class="bi" :class="icon"></i>
            </button>
        </div>
        <EditorContent :editor="editor" class="editor-content" />
    </div>
</template>
