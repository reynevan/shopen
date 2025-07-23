<div class="navigation sm:flex justify-center flex-col sm:flex-row">
    @foreach($block->getCategories() as $category)
        <div class="p-2 group/category nav">
            <a href="{{ $category->url }}" class="nav-category-link {{ $block->getCssClasses($category) }}" >
                {{ $category->name }}   {{ $category->level }}
            </a>
            @if ($category->subcategories)
                <div class="nav-category flex invisible absolute z-50 group-hover/category:visible ">
                    @foreach ($category->subcategories ?? [] as $subcategory)
                        <div class="group/subcategory nav">
                            <a href="{{ $subcategory->url }}" class="nav-subcategory-link block {{ $block->getCssClasses($subcategory) }}">
                                {{ $subcategory->name }}
                            </a>
                            <div>
                                @foreach ($subcategory->subcategories ?? [] as $subcategory2)
                                    <div class="nav">
                                        <a href="{{ $subcategory2->url }}" class="nav-subcategory-2-link block {{ $block->getCssClasses($subcategory2) }}">
                                            {{ $subcategory2->name }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>