<div class="filter-pills">
    <button
        type="button"
        class="pill"
        :class="{ active: categoryId === '' }"
        x-on:click="categoryId = ''"
    >
        Semua
    </button>
    <template x-for="c in categories" :key="c.id">
        <button
            type="button"
            class="pill"
            :class="{ active: String(categoryId) === String(c.id) }"
            x-on:click="categoryId = String(c.id)"
            x-text="c.nama_kategori"
        ></button>
    </template>
</div>
