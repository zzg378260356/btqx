<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle(@js($getStatePath())),
            groups: @js($groups),
            has(permission) { return Array.isArray(this.state) && this.state.includes(permission) },
            allSelected(children) { return Object.keys(children).every(permission => this.has(permission)) },
            someSelected(children) { return Object.keys(children).some(permission => this.has(permission)) },
            togglePermission(permission, checked) {
                const selected = new Set(Array.isArray(this.state) ? this.state : [])
                checked ? selected.add(permission) : selected.delete(permission)
                this.state = [...selected]
            },
            toggleGroup(children, checked) {
                const selected = new Set(Array.isArray(this.state) ? this.state : [])
                for (const permission of Object.keys(children)) {
                    checked ? selected.add(permission) : selected.delete(permission)
                }
                this.state = [...selected]
            },
        }"
        class="space-y-3"
    >
        <template x-for="group in groups" :key="group.label">
            <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                <label class="flex cursor-pointer items-center gap-2 font-semibold">
                    <input
                        type="checkbox"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600"
                        :checked="allSelected(group.children)"
                        x-effect="$el.indeterminate = someSelected(group.children) && !allSelected(group.children)"
                        @change="toggleGroup(group.children, $event.target.checked)"
                    >
                    <span x-text="group.label"></span>
                </label>
                <div class="mt-3 grid grid-cols-2 gap-3 pl-6 sm:grid-cols-4">
                    <template x-for="(label, permission) in group.children" :key="permission">
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600"
                                :checked="has(permission)"
                                @change="togglePermission(permission, $event.target.checked)"
                            >
                            <span x-text="label"></span>
                        </label>
                    </template>
                </div>
            </div>
        </template>
    </div>
</x-dynamic-component>
