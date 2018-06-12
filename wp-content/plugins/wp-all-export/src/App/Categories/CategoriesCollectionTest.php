<?php


class CategoriesCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanBeInitiated()
    {
        $categories = array('id' => 0,
            'title' => 'Root',
            'children' => array(0 => array('id' => 33, 'title' => 'Animal Supplies', 'children' => array(0 => array('id' => 35, 'title' => 'Peeet Supplies', 'children' => array(0 => array('id' => 36, 'title' => 'Pet Doors', 'children' => array(), 'selectedCategory' => 'Cameras & Optics', 'selectedCategoryId' => '141',),), 'selectedCategory' => 'Cameras & Optics', 'selectedCategoryId' => '141',),), 'selectedCategory' => 'Cameras & Optics', 'selectedCategoryId' => '141',), 1 => array('id' => 23, 'title' => 'Clothes', 'children' => array(0 => array('id' => 24, 'title' => 'Shirts', 'children' => array(), 'selectedCategory' => 'Cameras & Optics', 'selectedCategoryId' => '141',),), 'selectedCategory' => 'Cameras & Optics', 'selectedCategoryId' => '141',), 2 => array('id' => 37, 'title' => 'Furniture', 'children' => array(0 => array('id' => 38, 'title' => 'Chairs', 'children' => array(0 => array('id' => 39, 'title' => 'Bean bag chair', 'children' => array(), 'selectedCategory' => 'Apparel & Accessories', 'selectedCategoryId' => '166',),), 'selectedCategory' => 'Apparel & Accessories', 'selectedCategoryId' => '166',),), 'selectedCategory' => 'Apparel & Accessories', 'selectedCategoryId' => '166',), 3 => array('id' => 26, 'title' => 'Head wear', 'children' => array(0 => array('id' => 28, 'title' => 'Glasses', 'children' => array(), 'selectedCategory' => 'Cameras & Optics', 'selectedCategoryId' => '141',), 1 => array('id' => 27, 'title' => 'Hats', 'children' => array(), 'selectedCategory' => 'Cameras & Optics', 'selectedCategoryId' => '141',),), 'selectedCategory' => 'Cameras & Optics', 'selectedCategoryId' => '141',), 4 => array('id' => 29, 'title' => 'Kitchen', 'children' => array(0 => array('id' => 30, 'title' => 'Mugs', 'children' => array(), 'selectedCategory' => 'Business & Industrial', 'selectedCategoryId' => '111',),), 'selectedCategory' => 'Business & Industrial', 'selectedCategoryId' => '111',), 5 => array('id' => 34, 'title' => 'Pet Supplies', 'children' => array(), 'selectedCategory' => 'Apparel & Accessories', 'selectedCategoryId' => '166',),),);
        $categoriesCollection = new \Wpae\App\Categories\CategoriesCollection($categories);
        $this->assertInstanceOf(\Wpae\App\Categories\CategoriesCollection::class, $categoriesCollection);

        $cat = $categoriesCollection->findCategory('Mugs');
        print_r($cat);

    }
    
}
