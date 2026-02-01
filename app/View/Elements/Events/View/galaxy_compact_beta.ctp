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
 */
?>
<?php if (!empty($clusters)): ?>
    <div class="beta-galaxy-wrapper">
        <?php foreach ($clusters as $cluster): ?>
            <?php 
                $val = is_array($cluster) ? $cluster['value'] : $cluster;
                $id = is_array($cluster) ? ($cluster['id'] ?? null) : null;
                $local = is_array($cluster) ? ($cluster['local'] ?? false) : false;
                $relBefore = is_array($cluster) ? ($cluster['relationship_type'] ?? null) : null;
                $relAfter = is_array($cluster) ? ($cluster['relationship'] ?? null) : null;
            ?>
            <div class="beta-galaxy-cluster">
                <div class="beta-galaxy-header">
                    <i class="fas fa-star beta-galaxy-icon-star"></i>
                    <i class="fas fa-<?php echo $local ? 'user' : 'globe-americas'; ?> beta-galaxy-icon-scope" title="<?php echo $local ? __('Local') : __('Public'); ?>"></i>
                    
                    <span class="beta-galaxy-cluster-label"><?php echo h(strtoupper($galaxyName)); ?></span>
                    
                    <?php if ($relBefore): ?>
                        <span class="beta-galaxy-relationship">(<?php echo h($relBefore); ?>)</span>
                    <?php endif; ?>
                </div>

                <div class="beta-galaxy-cluster-values">
                    <?php if ($id && !empty($baseurl)): ?>
                        <a href="<?php echo $baseurl; ?>/galaxy_clusters/view/<?php echo h($id); ?>" class="beta-galaxy-link"><?php echo h($val); ?></a>
                    <?php else: ?>
                        <?php echo h($val); ?>
                    <?php endif; ?>
                </div>

                <?php if ($relAfter): ?>
                    <span class="beta-galaxy-relationship-after">[<?php echo h($relAfter); ?>]</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
