{{-- store/partials/category_chips.blade.php --}}
<section class="flex flex-wrap gap-2">
    <template x-for="cat in categories" :key="cat">
        <button @click="toggleCategory(cat)"
                class="px-3 py-1.5 rounded-full border text-xs font-semibold"
                :class="activeCategories.includes(cat)
                    ? 'bg-indigo-600 text-white border-indigo-600'
                    : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
            <span x-text="cat"></span>
        </button>
    </template>

    <button @click="clearCategories"
            class="px-3 py-1.5 rounded-full border text-xs text-slate-600 border-slate-200 hover:bg-slate-50">
        Clear filters
    </button>
</section>
