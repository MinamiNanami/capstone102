<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InventoryItem;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InventoryItem::create([
        'name' => 'Beef Dog Food',
        'category' => 'FOOD',
        'image' => 'images/5316229.jpg'
    ]);
        InventoryItem::create([
            'name' => 'Chicken Dog Food',
            'category' => 'FOOD',
            'image' => 'images/5316229.jpg'
        ]);

        InventoryItem::create([
            'name' => 'Dog Shampoo',
            'category' => 'GROOMING',
            'image' => 'images/5316229.jpg'
        ]);

        InventoryItem::create([
            'name' => 'Dog Flea Medicine',
            'category' => 'MEDICINE',
            'image' => 'images/5316229.jpg'
        ]);

        InventoryItem::create([
            'name' => 'Dog Vitamins',
            'category' => 'SUPPLEMENTS',
            'image' => 'images/5316229.jpg'
        ]);
    }
}
