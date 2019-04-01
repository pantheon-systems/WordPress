<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<?php
if ($this->step === 1000) {
    $wp_list_table = $this->get_list_table();
    $wp_list_table->prepare_items();
    $wp_list_table->views();
    ?>

    <form id="posts-filter" method="get">
        <?php $wp_list_table->search_box($this->post_type->labels->search_items, 'post'); ?>
        <input type="hidden" name="post_status" class="post_status_page" value="<?php echo!empty($_REQUEST['post_status']) ? esc_attr($_REQUEST['post_status']) : 'all'; ?>" />
        <?php
        $wp_list_table->display();
        ?>
    </form>

    <?php
    die();
}
?>

<div class="wrap bulk-edit-extended-permissions">
    <h2>
        <?php echo $this->__('Extended Permissions'); ?>
    </h2>

    <?php
    switch ($this->step) {
        case 1:
            ?>
            <form method="post">
                <?php $this->main->create_nonce('bulk_edit'); ?> 
                <p>
                    <strong><?php echo $this->__('Step 1: Select the post type to bulk edit'); ?></strong>
                </p>
                <?php
                foreach ($this->get_custom_post_types() as $key => $value) {
                    ?>
                    <p>
                        <label>
                            <input type="radio" name="post-type" value="<?php echo $key ?>" /><?php echo $value; ?>       
                        </label>
                    </p>
                    <?php
                }
                ?>
                <input type="hidden" name="step" value="<?php echo $this->step; ?>" />

                <p class="submit">
                    <input type="submit" id="next-step" class="button button-primary" value="<?php echo $this->__('Next Step'); ?>" />
                </p>
            </form>
            <?php
            break;
        case 2:
            ?>
            <form method="post">
                <p class="hidden" id="step2-nonce">
                    <?php $this->main->create_nonce('bulk_edit'); ?> 
                </p>
                <p>
                    <strong><?php printf($this->__('Step 2: Select the %s to bulk edit'), $this->post_type->labels->name); ?></strong>
                </p>
                <p>
                    <label>
                        <input type="radio" name="select-posts" value="all" checked="true" /><?php printf($this->__('All %s'), $this->post_type->labels->name); ?>       
                    </label>
                </p>
                <p>
                    <label>
                        <input type="radio" name="select-posts" value="selected" /><?php printf($this->__('Selected %s'), $this->post_type->labels->name); ?>       
                    </label>
                </p>
                <p class="loading-image">
                    <img src="<?php echo $this->image_url() . 'loading.gif'; ?>" />
                </p>
                <p id="posts-container" class="hidden"></p>
                <input type="hidden" name="selected-posts" value="" />
                <input type="hidden" name="post-type" value="<?php echo $this->post_type->name; ?>" />
                <input type="hidden" name="step" value="<?php echo $this->step; ?>" />
                <p class="submit">
                    <input type="submit" id="next-step" class="button button-primary" value="<?php echo $this->__('Next Step'); ?>" />
                </p>
            </form>

            <script type="text/javascript">
                (function ($) {
                    var $container = $("#posts-container");

                    $("input[name='select-posts']").change(function () {
                        var $this = $(this);
                        if ($this.val() === "selected") {
                            $container.removeClass("hidden");
                        } else {
                            $container.addClass("hidden");
                        }
                    });

                    function setSelectedPosts() {
                        var $current = $container.next().val();
                        if ($current === "")
                            $current = " ";
                        else
                            $current = " " + $current + " ";

                        $container.find("tbody th:first-child input[type='checkbox']").each(function () {
                            var $this = $(this);
                            if ($this.prop("checked")) {
                                if ($current.indexOf(" " + $this.val() + " ") === -1) {
                                    $current = $current + $this.val() + " ";
                                }
                            } else {
                                $current = $current.replace(" " + $this.val() + " ", " ");
                            }
                        });

                        $container.next().val($.trim($current));
                    }

                    function loadContainer(href) {
                        if (href === "#")
                            return;

                        $container.html("").append($container.prev().clone().show());

                        var data = {};
                        var href = href.split("?");
                        if (href.length > 1) {
                            href.shift();
                            href = href.join("").split("&");
                            for (var i = 0; i < href.length; i++) {
                                var s = href[i].split("=");
                                data[s[0]] = s[1];
                            }
                        }

                        $.extend(data, {
                            "action": "wpfront_user_role_editor_bulk_edit_extended_permissions_posts_table",
                            "page": "wpfront-user-role-editor-bulk-edit",
                            "bulk-edit-type": "extended-permissions",
                            "post-type": "<?php echo $this->post_type->name; ?>"
                        });

                        $("#step2-nonce input").each(function () {
                            var $this = $(this);
                            data[$this.prop("name")] = $this.val();
                        });

                        var url = ajaxurl + "?page=wpfront-user-role-editor-bulk-edit&bulk-edit-type=extended-permissions";
                        $.post(url, data, function (response) {
                            $container.html(response);

                            $container.find("a.row-title").prop("href", "#");

                            $container.find("a").each(function () {
                                var $this = $(this);
                                $this.click(function () {
                                    loadContainer($this.attr("href"));
                                    return false;
                                });
                            });

                            $container.find("input[type='submit']").each(function () {
                                var $this = $(this);
                                $this.click(function () {
                                    var q = [];
                                    $container.find(":input").each(function () {
                                        var $t = $(this);
                                        if ($t.prop("type") === "submit")
                                            return;
                                        q.push($t.prop("name") + "=" + $t.val());
                                    });
                                    q.push($this.prop("name") + "=" + $this.val());
                                    loadContainer("?" + q.join("&"));
                                    return false;
                                });
                            });

                            $container.find("#cb-select-all-1, #cb-select-all-2").change(function () {
                                $container.find("th:first-child input[type='checkbox']").prop("checked", $(this).prop("checked"));
                                setSelectedPosts();
                            });

                            $container.find("tbody th:first-child input[type='checkbox']").each(function () {
                                var $this = $(this);
                                var $current = " " + $container.next().val() + " ";
                                $this.prop("checked", $current.indexOf(" " + $this.val() + " ") !== -1);
                            }).change(function () {
                                setSelectedPosts();
                            });
                        });
                    }

                    loadContainer('');
                })(jQuery);
            </script>

            <?php
            break;

        case 3:
            ?>
            <form method="post">
                <p class="hidden" id="step2-nonce">
                    <?php $this->main->create_nonce('bulk_edit'); ?> 
                </p>
                <p>
                    <strong><?php echo $this->__('Step 3: Select the role permissions.'); ?></strong>
                </p>
                <p>
                    <label><input type="radio" name="role-type" value="multiple" checked="true" /><?php echo $this->__('Multiple roles'); ?></label>
                </p>
                <div id="poststuff">
                    <div class="postbox">
                        <h3 class="hndle">
                            <span><?php echo $this->__('Role Permissions'); ?></span>
                        </h3>
                        <div class="inside">
                            <?php
                            $this->post_type_permissions_object->meta_box();
                            ?>
                        </div>
                    </div>
                </div>
                <p>
                    <label><input type="radio" name="role-type" value="single" /><?php echo $this->__('Single role'); ?></label>
                </p>
                <div id="single-role" style="display: none">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <select name="single-role-name">
                                    <?php
                                    $roles = $this->post_type_permissions_object->get_roles_data();

                                    foreach ($roles as $key => $value) {
                                        if ($key === self::ADMINISTRATOR_ROLE_KEY)
                                            continue;

                                        echo '<option value="' . $key . '">' . $value[0] . '</option>';
                                    }
                                    ?>
                                </select>
                            </th>
                            <td>
                                <label><input type="checkbox" name="single-role-permissions[read]" /><?php echo $this->__('Read'); ?>&#160;&#160;&#160;</label>
                                <label><input type="checkbox" name="single-role-permissions[edit]" /><?php echo $this->__('Edit'); ?>&#160;&#160;&#160;</label>
                                <label><input type="checkbox" name="single-role-permissions[delete]" /><?php echo $this->__('Delete'); ?>&#160;&#160;&#160;</label>
                            </td>
                        </tr>
                    </table>
                </div>
                <input type="hidden" name="post-type" value="<?php echo $this->post_type->name; ?>" />
                <input type="hidden" name="step" value="<?php echo $this->step; ?>" />
                <input type="hidden" name="select-posts" value="<?php echo $this->selected_posts === 'all' ? 'all' : 'selected'; ?>" />
                <input type="hidden" name="selected-posts" value="<?php echo $this->selected_posts; ?>" />
                <p class="submit">
                    <input type="submit" id="next-step" class="button button-primary" value="<?php echo $this->__('Next Step'); ?>" />
                </p>
            </form>

            <script type="text/javascript">
                (function ($) {
                    $('input[name="role-type"]').change(function () {
                        var $multiple = $('#poststuff');
                        var $single = $('#single-role');

                        if ($(this).val() == 'multiple') {
                            $multiple.show();
                            $single.hide();
                        } else {
                            $multiple.hide();
                            $single.show();
                        }
                    });
                })(jQuery);
            </script>
            <?php
            break;

        case 4:
            ?>
            <p class="hidden" id="step4-nonce">
                <?php $this->main->create_nonce('bulk_edit'); ?> 
            </p>
            <p>
                <strong><?php echo $this->__('Step 4: Update role permissions.'); ?></strong>
            </p>
            <p>
                <?php printf($this->__('%d %s selected to update.'), $this->get_posts_count(), $this->post_type->labels->name); ?>
            </p>
            <p class="submit">
                <input type="submit" id="next-step" class="button button-primary" value="<?php echo $this->__('Submit'); ?>" />
            </p>

            <script type="text/javascript">
                (function ($) {
                    var post_type = "<?php echo $this->post_type->name; ?>";
                    var posts = "<?php echo $this->selected_posts; ?>";
                    var count = <?php echo $this->get_posts_count(); ?>;
                    var index = 0;

                    var data = {
                        "action": "wpfront_user_role_editor_bulk_edit_extended_permissions_update_post",
                        "post-type": post_type,
                        "posts": posts,
                        "count": count,
                        "request-post": <?php echo json_encode($this->request_post); ?>
                    };

                    $("#step4-nonce input").each(function () {
                        var $this = $(this);
                        data[$this.prop("name")] = $this.val();
                    });

                    function process() {
                        if (count === 0)
                            return;

                        var $div = $('<div class="update-info-line" />');
                        $div.append($('<i class="fa fa-refresh fa-spin"></i>'));
                        $div.append($('<span class="sync-info">' + '<?php echo sprintf($this->__('Updating %s'), $this->post_type->labels->singular_name); ?> ' + (index + 1) + ' / ' + count + '</span>'));
                        $container.append($div);

                        $.extend(data, {"index": index});

                        var url = ajaxurl + "?page=wpfront-user-role-editor-bulk-edit&bulk-edit-type=extended-permissions";
                        $.post(url, data, function (response) {
                            if (response) {
                                if (response.success) {
                                    $div.children().first().removeClass('fa-refresh fa-spin').addClass('fa-check fa-1');
                                    $div.append($('<span>&mdash; ' + '<?php echo $this->__('SUCCESS'); ?>' + '</span>'));
                                    $div.append($('<span>&mdash; <a href=' + response.link + ' target="_blank">' + response.title + '</a></span>'));
                                } else {
                                    $div.children().first().removeClass('fa-refresh fa-spin').addClass('fa-times fa-1');
                                    $div.append($('<span>&mdash; ' + '<?php echo $this->__('FAIL'); ?>' + '</span>'));
                                    $div.append($('<span>&mdash; ' + response.error + '</span>'));
                                    if (response.title)
                                        $div.append($('<span>&mdash; <a href=' + response.link + ' target="_blank">' + response.title + '</a></span>'));
                                }

                                if (response.eof)
                                    return;

                                index++;
                                process();
                            }
                        }, "json");
                    }

                    var $container = $("#next-step").click(function () {
                        $(this).hide();
                        process();
                    }).parent();

                })(jQuery);
            </script>
        <?php
    }
    ?>
</div>