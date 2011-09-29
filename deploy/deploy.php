<?php
$commitNum = $argv[1];
echo "Deploy: " . $commitNum . "\n";
$fileList = explode("\n", shell_exec("git show " . $commitNum . " --name-status --pretty=format:"));
mkdir("./" . $commitNum, 0777);
chdir("./" . $commitNum);
foreach($fileList as $file){
    if(empty($file)) continue;
    
    $line = explode("\t", $file);
    $status = $line[0];
    $path = $line[1];
    
    if($status == "D") continue;
    
    //Prepara arvore de dirs
    $arrPath = explode("/", $path);
    $numPathLevels = count($arrPath);
    $leafFile = $arrPath[$numPathLevels-1];
    if($numPathLevels == 1)
        $dirTree = "";
    else
        $dirTree = substr ($path, 0, strlen ($path)-strlen ($leafFile));
    
    if(!is_dir("./" . $dirTree))
        mkdir ($dirTree, 0777, true);
    
    copy("../../" . $dirTree . $leafFile, "./" . $dirTree . $leafFile);
}
?>