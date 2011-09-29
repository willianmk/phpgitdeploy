<?php

$levelVer = $argv[1];

$arq = fopen("./version.txt", "r+");
$line = fread($arq, filesize("./version.txt"));
$fields = explode(";", $line);

$version = $fields[0];
$levels = explode(".", $version);

if ($levelVer > count($levels)) {
    for ($i=count($levels)-1; $i < $levelVer; $i++) {
        if(!empty($levels[$i])) continue;
        else $levels[$i]="0";
    }
    $levels[count($levels)-1]="1";
}
else
    $levels[$levelVer-1]++;

$writeVer = implode(".", $levels) . date(";Y-m-d H:i:s");
$arq = fopen("./version.txt", "w+");
fwrite($arq, $writeVer);

for ($i = 2; $i < $argc; $i++) {
    $commitNum = $argv[$i];
    echo "Deploy: " . $commitNum . "\n";
    $fileList = explode("\n", shell_exec("git show " . $commitNum . " --name-status --pretty=format:"));
    $writeDir = "./" . $writeVer . "_" . date("YmdHis");
    mkdir("./" . $writeDir, 0777);
    chdir("./" . $writeDir);

    foreach ($fileList as $file) {
        if (empty($file))
            continue;

        $line = explode("\t", $file);
        $status = $line[0];
        $path = $line[1];

        if ($status == "D")
            continue;

        //Prepara arvore de dirs
        $arrPath = explode("/", $path);
        $numPathLevels = count($arrPath);
        $leafFile = $arrPath[$numPathLevels - 1];
        if ($numPathLevels == 1)
            $dirTree = "";
        else
            $dirTree = substr($path, 0, strlen($path) - strlen($leafFile));

        if (!is_dir("./" . $dirTree))
            mkdir($dirTree, 0777, true);

        copy("../../" . $dirTree . $leafFile, "./" . $dirTree . $leafFile);
    }
}
?>