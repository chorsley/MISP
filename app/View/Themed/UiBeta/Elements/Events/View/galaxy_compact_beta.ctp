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
        ?>
        <?php foreach ($clusters as $cluster): ?>
            <?php 
                $val = is_array($cluster) ? $cluster['value'] : $cluster;
                $id = is_array($cluster) ? ($cluster['id'] ?? null) : null;
                $local = is_array($cluster) ? ($cluster['local'] ?? false) : false;
                $relBefore = is_array($cluster) ? ($cluster['relationship_type'] ?? null) : null;
                $relAfter = is_array($cluster) ? ($cluster['relationship'] ?? null) : null;
            ?>
            <div class="beta-galaxy-cluster" data-pattern="<?php echo $patternIndex; ?>" style="background-color: <?php echo $bgColor; ?>; border-color: <?php echo $borderColor; ?>;">
                <div class="beta-galaxy-header">
                    <i class="fas fa-star beta-galaxy-icon-star"></i>
                    <i class="fas fa-<?php echo $local ? 'user' : 'globe-americas'; ?> beta-galaxy-icon-scope" title="<?php echo $local ? __('Local') : __('Public'); ?>"></i>
                    
                    <span class="beta-galaxy-cluster-label" style="color: <?php echo $labelColor; ?>;"><?php echo h(strtoupper($galaxyName)); ?></span>
                    
                    <?php if ($relBefore): ?>
                        <span class="beta-galaxy-relationship">(<?php echo h($relBefore); ?>)</span>
                    <?php endif; ?>
                </div>

                <div class="beta-galaxy-cluster-values">
                    <?php if ($id && !empty($baseurl)): ?>
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
