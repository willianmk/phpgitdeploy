<?php

$levelVer = $argv[1]; //Captures the versioning level

$arq = fopen("./version.txt", "r+"); //Open the version file
$line = fread($arq, filesize("./version.txt"));
$fields = explode(";", $line);

$version = $fields[0]; //Captures the version number
$levels = explode(".", $version); //Separates the version number elements

if ($levelVer > count($levels)) { //If the updated version is greater than the existant one...
    for ($i = count($levels) - 1; $i < $levelVer; $i++) {
        if (!empty($levels[$i]))
            continue;
        else
            $levels[$i] = "0"; //... fills with zeros
    }
    $levels[count($levels) - 1] = "1";
}
else {
    $levels[$levelVer - 1]++; // ... increments the version level ...
    $levels = array_slice($levels, 0, $levelVer, false); // and cuts off smaller version numbers
}

$version = implode(".", $levels); //Rebuilds tthe version number
$writeVer = implode(".", $levels) . date(";Y-m-d H:i:s"); //Prepares the format to be written onto the file version.txt
$arq = fopen("./version.txt", "w+"); // Empties the file version.txt
fwrite($arq, $writeVer);

$writeDir = "./" . $version . "_" . date("Ymd_His");

if (!is_dir($writeDir)) {
    mkdir("./" . $writeDir, 0777);
    chdir("./" . $writeDir);
}

for ($i = 2; $i < $argc; $i++) {
    $commitNum = $argv[$i];
    echo "\nCommit number: " . $commitNum . "\n";
    $fileList = explode("\n", shell_exec("git show " . $commitNum . " --name-status --pretty=format:"));

    foreach ($fileList as $file) {
        if (empty($file))
            continue;

        $line = explode("\t", $file);
        $status = $line[0];
        $path = $line[1];

        if ($status == "D")
            continue;

        //Prepares dir tree
        $arrPath = explode("/", $path);
        $numPathLevels = count($arrPath);
        $leafFile = $arrPath[$numPathLevels - 1];
        if ($numPathLevels == 1)
            $dirTree = "";
        else
            $dirTree = substr($path, 0, strlen($path) - strlen($leafFile));

        if (!is_dir("./" . $dirTree))
            mkdir($dirTree, 0777, true);

        echo "Copying file: " . $dirTree . $leafFile . "\n";
        copy("../../" . $dirTree . $leafFile, "./" . $dirTree . $leafFile);
    }
}
?>