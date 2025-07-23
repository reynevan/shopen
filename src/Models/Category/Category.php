<?php

namespace Shopen\Models\Category;

use Illuminate\Database\Eloquent\Model;
use Shopen\Models\Category\Attribute\CategoryAttribute;
use Shopen\Models\Interfaces\HasCustomAttributesInterface;
use Shopen\Models\Product\Product;
use Shopen\Models\Traits\HasCustomAttributes;
use Shopen\Models\Traits\HasUrl;
use Shopen\Models\UrlRewrite;
use Shopen\Models\Traits\HasSeoDetails;

class Category extends Model implements HasCustomAttributesInterface
{
    use HasCustomAttributes, HasUrl, HasSeoDetails;

    const ENTITY_TYPE = 'category';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
        'is_active',
        'image_path_desktop',
        'image_path_mobile',
    ];

    protected function getAttributeClass(): string
    {
        return CategoryAttribute::class;
    }

    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }

    protected function getOriginalAttributes(): array
    {
        return [
            'id',
            'parent_id',
            'level',
            'sort_index',
            'created_at',
            'updated_at',
            'is_active',
            'image_path_desktop',
            'image_path_mobile',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_index');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_products');
    }

    public function hasProduct(Product $product): bool
    {
        return $this->products()->where('product_id', $product->id)->exists();
    }

    public function urlRewrites()
    {
        return $this->hasMany(UrlRewrite::class, 'entity_id')->where('entity_type', self::ENTITY_TYPE);
    }

    public function getUrl()
    {
        $urlRewrite = $this->urlRewrites()->first();
        if (!$urlRewrite) {
            return null;
        }
        return config('app.url') . '/' . $urlRewrite->request_path;
    }

    protected function getDefaultSeoTitle(): string
    {
        return "$this->name - kup online ";
    }

    protected function getDefaultSeoDescription(): string
    {
        $appName = config('app.name');
        return "$this->name  w $appName. Zobacz bogatą ofertę!";
    }

}
