<?php
if (!empty($object['Galaxy'])):
    $subClustersByGalaxy = [];
    foreach ($object['Galaxy'] as $galaxy) {
        foreach ($galaxy['GalaxyCluster'] as $cluster) {
            $subClustersByGalaxy[$galaxy['name']][] = $cluster;
        }
    }
    foreach ($subClustersByGalaxy as $galaxyName => $clusters):
        echo $this->element('Events/View/galaxy_compact_beta', [
            'galaxyName' => $galaxyName,
            'clusters' => $clusters,
            'baseurl' => $baseurl
        ]);
    endforeach;
endif;
