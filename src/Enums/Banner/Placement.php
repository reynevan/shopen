<?php

namespace Shopen\Enums\Banner;

enum Placement: string
{
    case ALL_PAGE_TOP = 'all_page_top';
    case ALL_PAGE_BOTTOM = 'all_page_bottom';
    case HOME_PAGE_TOP = 'home_page_top';
    case HOME_PAGE_BOTTOM = 'home_page_bottom';
    case CATEGORY_PAGE_TOP = 'category_page_top';
    case CATEGORY_PAGE_BOTTOM = 'category_page_bottom';
    case CATEGORY_PAGE_PRODUCTS_TOP = 'category_page_products_top';
    case CATEGORY_PAGE_PRODUCTS_BOTTOM = 'category_page_products_bottom';
    case PRODUCT_PAGE_TOP = 'product_page_top';
    case PRODUCT_PAGE_BOTTOM = 'product_page_bottom';


    public function label(): string
    {
        return match ($this) {
            self::ALL_PAGE_TOP => 'Każda strona - na górze',
            self::ALL_PAGE_BOTTOM => 'Każda strona - na dole',
            self::HOME_PAGE_TOP => 'Strona główna - na górze',
            self::HOME_PAGE_BOTTOM => 'Strona główna - na dole',
            self::CATEGORY_PAGE_TOP => 'Strona kategorii - na górze',
            self::CATEGORY_PAGE_BOTTOM => 'Strona kategorii - na dole',
            self::CATEGORY_PAGE_PRODUCTS_TOP => 'Strona kategorii - nad produktami',
            self::CATEGORY_PAGE_PRODUCTS_BOTTOM => 'Strona kategorii - pod produktami',
            self::PRODUCT_PAGE_TOP => 'Strona produktu - na górze',
            self::PRODUCT_PAGE_BOTTOM => 'Strona produktu - na dole',
        };
    }


    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
