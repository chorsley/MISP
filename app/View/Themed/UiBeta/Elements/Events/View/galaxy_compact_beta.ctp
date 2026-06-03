<?php
/**
 * Compact Galaxy Tag Element
 * 
 * Renders a list of clusters for a galaxy in a compact tag format.
 * 
 * Variables:
 * - $galaxyName: string
 * - $clusters: array of GalaxyCluster objects (or at least array with 'value', 'tag_id', etc.)
 * - $preview: bool (optional)
 * - $baseurl: string
 * - $canModify: bool (optional) - whether the user can modify/delete galaxy tags
 * - $canModifyLocal: bool (optional) - whether the user can modify local galaxy tags
 * - $target_type: string (optional) - 'event' or 'attribute'
 * - $target_id: int (optional) - the ID of the event or attribute
 */
?>
<?php if (!empty($clusters)): ?>
    <div class="beta-galaxy-wrapper">
        <?php 
            // Generate deterministic color based on galaxy name
            $hash = abs(crc32($galaxyName));
            $hue = $hash % 360;
            // Vary saturation/lightness per galaxy to reduce similar-looking hues
            $saturation = 55 + ($hash % 3) * 10; // 55, 65, 75
            // Lightness levels for different parts
            $bgLightness = 96 - ($hash % 2); // 96-95
            $borderLightness = 82 - ($hash % 3); // 82-80
            $textLightness = 28 + ($hash % 3) * 6; // 28, 34, 40
            $labelColor = "hsl($hue, $saturation%, $textLightness%)";
            $bgColor = "hsl($hue, $saturation%, $bgLightness%)";
            $borderColor = "hsl($hue, $saturation%, $borderLightness%)";
            $patternIndex = $hash % 4;

            $canModify = $canModify ?? false;
            $canModifyLocal = $canModifyLocal ?? false;
            $target_type = $target_type ?? null;
            $target_id = $target_id ?? null;
        ?>
        <?php foreach ($clusters as $cluster): ?>
            <?php 
                $val = is_array($cluster) ? $cluster['value'] : $cluster;
                $id = is_array($cluster) ? ($cluster['id'] ?? null) : null;
                $local = is_array($cluster) ? ($cluster['local'] ?? false) : false;
                $relBefore = is_array($cluster) ? ($cluster['relationship_type'] ?? null) : null;
                $relAfter = is_array($cluster) ? ($cluster['relationship'] ?? null) : null;
                $tagId = is_array($cluster) ? ($cluster['tag_id'] ?? null) : null;
                // event_tag_id or attribute_tag_id depending on context
                $targetTagId = null;
                if ($target_type && is_array($cluster)) {
                    $targetTagId = $cluster[$target_type . '_tag_id'] ?? null;
                }

                $hasUtilityActions = !empty($id) || !empty($tagId);
                $showEditActions = ($canModify || ($canModifyLocal && $local)) && $target_type && $target_id;
                $showActions = $hasUtilityActions || $showEditActions;
            ?>
            <div class="beta-galaxy-cluster<?php echo $showActions ? ' beta-galaxy-cluster-editable' : ''; ?>" data-pattern="<?php echo $patternIndex; ?>" style="background-color: <?php echo $bgColor; ?>; border-color: <?php echo $borderColor; ?>;">
                <div class="beta-galaxy-header">
                    <i class="fas fa-star beta-galaxy-icon-star"></i>
                    <i class="fas fa-<?php echo $local ? 'user' : 'globe-americas'; ?> beta-galaxy-icon-scope" title="<?php echo $local ? __('Local') : __('Public'); ?>"></i>
                    
                    <span class="beta-galaxy-cluster-label" style="color: <?php echo $labelColor; ?>;"><?php echo h(strtoupper($galaxyName)); ?></span>
                    
                    <?php if ($relBefore): ?>
                        <span class="beta-galaxy-relationship">(<?php echo h($relBefore); ?>)</span>
                    <?php endif; ?>

                    <?php if ($showActions): ?>
                        <div class="beta-galaxy-actions noPrint">
                            <span class="beta-galaxy-actions-toggle" title="<?php echo __('Actions'); ?>"><i class="fas fa-caret-down"></i></span>
                            <div class="beta-galaxy-actions-dropdown">
                                <?php if ($id): ?>
                                    <a href="<?php echo $baseurl; ?>/galaxy_clusters/view/<?php echo h($id); ?>" class="beta-galaxy-action-item">
                                        <i class="fas fa-sitemap"></i> <?php echo __('View cluster'); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($tagId): ?>
                                    <a href="<?php echo $baseurl; ?>/events/index/searchtag:<?php echo intval($tagId); ?>" class="beta-galaxy-action-item">
                                        <i class="fas fa-search"></i> <?php echo __('Search events'); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($showEditActions && $target_type !== 'tag_collection' && $targetTagId): ?>
                                    <a href="<?php echo $baseurl; ?>/tags/modifyTagRelationship/<?php echo h($target_type); ?>/<?php echo intval($targetTagId); ?>" class="beta-galaxy-action-item modal-open">
                                        <i class="fas fa-project-diagram"></i> <?php echo __('Modify relationship'); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($showEditActions && $tagId): ?>
                                    <a href="<?php echo $baseurl; ?>/galaxy_clusters/detach/<?php echo intval($target_id); ?>/<?php echo h($target_type); ?>/<?php echo intval($tagId); ?>"
                                       class="beta-galaxy-action-item beta-galaxy-action-delete"
                                       onclick="confirmClusterDetach(this, '<?php echo h($target_type); ?>', <?php echo intval($target_id); ?>); return false;"
                                       title="<?php echo __('Are you sure you want to detach %s from this %s?', h($val), h($target_type)); ?>">
                                        <i class="fas fa-trash"></i> <?php echo __('Detach'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="beta-galaxy-cluster-values">
                    <?php if ($tagId && !empty($baseurl)): ?>
                        <a href="<?php echo $baseurl; ?>/events/index/searchtag:<?php echo intval($tagId); ?>" class="beta-galaxy-link" style="color: <?php echo $labelColor; ?>;"><?php echo h($val); ?></a>
                    <?php elseif ($id && !empty($baseurl)): ?>
                        <a href="<?php echo $baseurl; ?>/galaxy_clusters/view/<?php echo h($id); ?>" class="beta-galaxy-link" style="color: <?php echo $labelColor; ?>;"><?php echo h($val); ?></a>
                    <?php else: ?>
                        <span style="color: <?php echo $labelColor; ?>;"><?php echo h($val); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($relAfter): ?>
                    <span class="beta-galaxy-relationship-after">[<?php echo h($relAfter); ?>]</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<script>
(function() {
    // Galaxy compact tag dropdown toggle - initialize once per page
    if (window._galaxyCompactBetaInit) return;
    window._galaxyCompactBetaInit = true;

    $(document).on('click', '.beta-galaxy-actions-toggle', function(e) {
        e.stopPropagation();
        e.preventDefault();
        var $actions = $(this).closest('.beta-galaxy-actions');
        var wasOpen = $actions.hasClass('open');
        // Close all open dropdowns
        $('.beta-galaxy-actions.open').removeClass('open');
        if (!wasOpen) {
            $actions.addClass('open');
        }
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.beta-galaxy-actions').length) {
            $('.beta-galaxy-actions.open').removeClass('open');
        }
    });

    // Close dropdown on ESC
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('.beta-galaxy-actions.open').removeClass('open');
        }
    });
})();
</script>
