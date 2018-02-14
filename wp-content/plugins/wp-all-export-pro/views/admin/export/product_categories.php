<?php
$categoriesService = new \Wpae\App\Service\CategoriesService();
$categoriesHeirarchy = $categoriesService->getTaxonomyHierarchy();
function renderCategory($categories, $level)
{
    foreach ($categories as $category) {
        ?>
        <li style="display: block;">
            <div class="category-container" style="position: relative;"
                 ng-class="{ 'with-children' : node.children.length, 'without-children' : !node.children.length }">
                <div class="hline"></div>
                <div class="category-icon-container" style="float:left;">
                    <div class="vline" ng-if="($index > 0 && $dxLevel == 0) || $dxLevel > 0"></div>
                    <div class="vline noborder"
                         ng-if="!(($index > 0 && $dxLevel == 0) || $dxLevel > 0)"></div>
                    <span ng-if="node.expanded" class="minus" ng-click="expandNode(node)">
                        <svg width="9" height="9" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1600 736v192q0 40-28 68t-68 28h-1216q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h1216q40 0 68 28t28 68z"/>
                        </svg>
                    </span>
                    <span ng-if="!node.expanded && node.children.length" class="plus" ng-click="expandNode(node)">
                        <svg width="9" height="9" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"/>
                        </svg>
                    </span>
                                        <span ng-if="!node.children.length" class="plus blank"
                                              style="cursor: default;"></span>
                    <div class="vline bottom"></div>
                </div>
                <div class="category-name-container">
                    <span class="dot" ng-repeat="i in ::getTimes($dxLevel) track by $index"></span>
                    <div class="category">
                        <a class="category-title" href="" ng-click="expandNode(node)"><?php echo $category['title']; ?></a>
                        <br ng-if="node.children.length"/>
                        <span ng-if="node.children.length" class="children-number">
                            {{ ::node.children.length }} child <span ng-if="::node.children.length == 1">category</span><span
                                ng-if="::node.children.length > 1">categories</span>
                        </span>
                    </div>
                </div>
                <div class="line"></div>
                <div class="mapping" ng-if="context == 'categories'">
                    <google-category-selector-adder selected-category="node.selectedCategory"
                                                    selected-category-id="node.selectedCategoryId"/>
                </div>
                <div class="mapping gender" ng-if="context == 'gender'" style="border: none;">
                    <select chosen cascade="gender" ng-model="node.selectedGender"
                            ng-change="selectGender()">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="unisex">Unisex</option>
                    </select>
                </div>
                <div class="mapping" ng-if="context == 'ageGroup'"
                     style="border: none; background-color: #F1F1F1; padding:0; margin-top: 5px;">
                    <select chosen cascade="ageGroup" ng-model="node.selectedAgeGroup"
                            ng-change="selectAgeGroup()">
                        <option value="newborn">Newborn</option>
                        <option value="infant">Infant</option>
                        <option value="toddler">Toddler</option>
                        <option value="kids">Kids</option>
                        <option value="adult">Adult</option>
                    </select>
                </div>
                <div style="clear:both;"></div>
            </div>
            <ul dx-connect="node" ng-if="node.expanded==true"/>
        </li>
        <?php
        echo $category['title'] . "\n";
        renderCategory($category['children'], $level + 1);
    }
}

//renderCategory($categoriesHeirarchy, 0);
?>
<div class="wpallexport-collapsed wpallexport-section" ng-class="{closed: !merchantsFeedData.productCategories.open}"
     ng-controller="productCategoriesController">
    <div class="wpallexport-content-section">
        <div class="wpallexport-collapsed-header disable-jquery"
             ng-click="merchantsFeedData.productCategories.open = !merchantsFeedData.productCategories.open">
            <h3>Product Categories</h3>
        </div>
        <div class="wpallexport-collapsed-content" ng-slide-down="merchantsFeedData.productCategories.open"
             duration="0.5">
            <div class="wpallexport-collapsed-content-inner">
                <h4>Product Type</h4>
                <p>Use this attribute to classify the product using your own categories. The categories here don't need
                    to match Google's list of acceptable product categories.</p>
                <div class="input">
                    <label>
                        <input type="radio" ng-model="merchantsFeedData.productCategories.productType"
                               value="useWooCommerceProductCategories"/>Use WooCommerce's product category
                    </label>
                </div>
                <div class="input">
                    <label>
                        <input type="radio" ng-model="merchantsFeedData.productCategories.productType"
                               value="customValue"/>Custom data
                    </label>
                    <div class="input inner"
                         ng-slide-down="merchantsFeedData.productCategories.productType == 'customValue'"
                         duration="0.2">
                        <input type="text" class="wpae-default-input"
                               ng-model="merchantsFeedData.productCategories.productTypeCV" droppable/>
                    </div>
                </div>
                <h4>Product Category</h4>
                <p>
                    Products added to Google Merchant Center must be categorized according to Google's list of product
                    categories. Each product may only be assigned one Google product category. <a
                        href="https://support.google.com/merchants/answer/160081" target="_blank">Read more about Google
                        product categories.</a>
                </p>
                <div class="input">
                    <label>
                        <input type="radio" ng-model="merchantsFeedData.productCategories.productCategories"
                               value="mapProductCategories"/>Map WooCommerce's product categories to Google's product
                        categories
                        <a href="#" class="wpallexport-help" style="margin-top:5px; margin-left: 2px;"
                           tipsy="Products assigned more than one WooCommerce product category and mapped to more than one Google product category will be mapped to the most specific, deepest Google product category selected for that product.">?</a>
                    </label>
                </div>
                <div ng-slide-down="merchantsFeedData.productCategories.productCategories == 'mapProductCategories'"
                     duration="0.5">
                    <!-- Begin inline category mapper -->

                    <div class="category-mapper" ng-if="mapping.children.length">
                        <div>
                            <div class="woocommerce-categories-title" style="float:left; padding: 13px 13px 13px 31px;">
                                <h4 style="margin: 0; padding: 0; font-size:13px; color:#000;">WooCommerce
                                    Categories</h4>
                            </div>

                            <div class="google-categories-title" style="float:right; padding:13px; margin-right: 278px;"
                                 ng-if="context=='categories'">
                                <h4 style="margin:0; padding:0; font-size:13px; color:#000; ">Google Categories</h4>
                            </div>

                            <div class="google-categories-title" style="float:right; padding:13px; margin-right: 288px;"
                                 ng-if="context=='gender'">
                                <h4 style="margin:0; padding:0; font-size:13px; color:#000; ">Google Genders</h4>
                            </div>

                            <div class="google-categories-title" style="float:right; padding:13px; margin-right: 268px;"
                                 ng-if="context=='ageGroup'">
                                <h4 style="margin:0; padding:0; font-size:13px; color:#000; ">Google Age Groups</h4>
                            </div>
                        </div>

                        <ul dx-start-with="innerMapping" class="tree" style="width: 100%; float:left;">
                            <?php echo renderCategory($categoriesHeirarchy, 0);?>
                            <!--<li ng-repeat="node in $dxPrior.children | limitTo: limits" style="display: block;">
                                <div class="category-container" style="position: relative;"
                                     ng-class="{ 'with-children' : node.children.length, 'without-children' : !node.children.length }">
                                    <div class="hline"></div>
                                    <div class="category-icon-container" style="float:left;">
                                        <div class="vline" ng-if="($index > 0 && $dxLevel == 0) || $dxLevel > 0"></div>
                                        <div class="vline noborder"
                                             ng-if="!(($index > 0 && $dxLevel == 0) || $dxLevel > 0)"></div>
                    <span ng-if="node.expanded" class="minus" ng-click="expandNode(node)">
                        <svg width="9" height="9" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1600 736v192q0 40-28 68t-68 28h-1216q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h1216q40 0 68 28t28 68z"/>
                        </svg>
                    </span>
                    <span ng-if="!node.expanded && node.children.length" class="plus" ng-click="expandNode(node)">
                        <svg width="9" height="9" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"/>
                        </svg>
                    </span>
                                        <span ng-if="!node.children.length" class="plus blank"
                                              style="cursor: default;"></span>
                                        <div class="vline bottom"></div>
                                    </div>
                                    <div class="category-name-container">
                                        <span class="dot" ng-repeat="i in ::getTimes($dxLevel) track by $index"></span>
                                        <div class="category">
                                            <a class="category-title" href="" ng-click="expandNode(node)">{{
                                                ::node.title }}</a>
                                            <br ng-if="node.children.length"/>
                        <span ng-if="node.children.length" class="children-number">
                            {{ ::node.children.length }} child <span ng-if="::node.children.length == 1">category</span><span
                                ng-if="::node.children.length > 1">categories</span>
                        </span>
                                        </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="mapping" ng-if="context == 'categories'">
                                        <google-category-selector-adder selected-category="node.selectedCategory"
                                                                        selected-category-id="node.selectedCategoryId"/>
                                    </div>
                                    <div class="mapping gender" ng-if="context == 'gender'" style="border: none;">
                                        <select chosen cascade="gender" ng-model="node.selectedGender"
                                                ng-change="selectGender()">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="unisex">Unisex</option>
                                        </select>
                                    </div>
                                    <div class="mapping" ng-if="context == 'ageGroup'"
                                         style="border: none; background-color: #F1F1F1; padding:0; margin-top: 5px;">
                                        <select chosen cascade="ageGroup" ng-model="node.selectedAgeGroup"
                                                ng-change="selectAgeGroup()">
                                            <option value="newborn">Newborn</option>
                                            <option value="infant">Infant</option>
                                            <option value="toddler">Toddler</option>
                                            <option value="kids">Kids</option>
                                            <option value="adult">Adult</option>
                                        </select>
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
                                <ul dx-connect="node" ng-if="node.expanded==true"/>
                            </li>-->
                        </ul>
                        <div class='catList' style="clear:both;"></div>
                        <div class="mask" ng-class="{ grey : grey == 1}"></div>
                    </div>
                    <div ng-if="initialized && !innerMapping.children.length">
                        <div ng-include="'productCategories/categoryMapper/noCategoriesNotice.tpl.html'"></div>
                    </div>

                    <!-- End inline category mapper -->
                </div>

                <div class="input">
                    <label>
                        <input type="radio" ng-model="merchantsFeedData.productCategories.productCategories"
                               value="useWooCommerceProductCategories"/>Use WooCommerce's product categories
                        <a href="#" class="wpallexport-help" style="margin-top:5px; margin-left: 2px;"
                           tipsy="Products assigned to more than one WooCommerce product category will only have the most specific, deepest product category exported.">?</a>
                    </label>
                    <div
                        ng-slide-down="!merchantsFeedData.productCategories.cats.children.length && merchantsFeedData.productCategories.productCategories == 'useWooCommerceProductCategories'"
                        duration="0.2">
                        <div ng-include="'productCategories/categoryMapper/noCategoriesNotice.tpl.html'"
                             ng-init="context = 'categories' "></div>
                    </div>
                </div>

                <div class="input">
                    <label>
                        <input type="radio" ng-model="merchantsFeedData.productCategories.productCategories"
                               value="customValue"/>Custom data
                    </label>
                    <div class="input inner"
                         ng-slide-down="merchantsFeedData.productCategories.productCategories == 'customValue'"
                         duration="0.2">
                        <input type="text" class="wpae-default-input"
                               ng-model="merchantsFeedData.productCategories.productCategoriesCV" droppable/>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>