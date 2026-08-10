<a href="#menu-category-{{ $category->id }}"
   @click.prevent="const el = document.querySelector($event.currentTarget.getAttribute('href')); if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
   class="badge badge-soft {{ $badgeColor }} badge-lg gap-1.5 hover:opacity-90 transition-opacity cursor-pointer no-underline whitespace-nowrap">
    <span class="{{ $category->icon_class ?: 'icon-[tabler--bowl-chopsticks]' }} size-4"></span>
    {{ $category->name }}
</a>
