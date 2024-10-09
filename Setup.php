<?php namespace rogoss\workspace;

class SetupException extends \Exception {
    const LIB_NOT_A_DIRECTORY = 1;
    const LIB_HAS_NO_MODULES = 2;
    const LIB_MODULES_READ_ERROR = 3;
    const FILE_EXISTS = 4;
}

class Setup {

    private $sRootPath = "";
    private $aModules = [];

//==============================================================================
// Creator Functions
//==============================================================================
    public static function create($sRootPath) {
        if(!is_dir($sPath = rtrim($sRootPath, "\\/"))) throw new SetupException("$sPath is not a Directory", SetupException::LIB_NOT_A_DIRECTORY); 
        $oI = new static();
        $oI->sRootPath = $sPath;
        return $oI; 
    }

//==============================================================================
// Builder functions
//==============================================================================
    public function addLib($sPath) {
        $sPath = trim($sPath, "\\/");

        if(!is_dir( $this->sRootPath . "/" . $sPath)) throw new SetupException("$sPath is not a Directory", SetupException::LIB_NOT_A_DIRECTORY); 
        $sFile = $this->sRootPath . "/" . $sPath . "/_modules.php";

        if(!file_exists($sFile)) throw new SetupException("$sPath does not contain a _modules.php", SetupException::LIB_HAS_NO_MODULES); 
        
        include $sFile;
        $aModules = $aModules ?? [];
        if(!is_array($aModules)) throw new SetupException("$sPath/_modules.php has an invalid format. Use `./config $sPath` script to set it up", SetupException::LIB_MODULES_READ_ERROR);

        foreach($aModules as $sClass => $sFile) {
            $sFile = $sPath . "/" . $sFile;
            $this->aModules[$sClass] = $sFile;
        }

        return $this;
    }

//==============================================================================
// Methods 
//==============================================================================
    
    function compile($sOutFileName="autoload.php") {

        //TODO: Process Subfolders
        $sPathPrefix = str_repeat("../", count(explode("/", ltrim($sOutFileName, "./")))-1);

        $aLocalizedModules = [];
        foreach($this->aModules as $sClass => $sFile)
            $aLocalizedModules[$sClass] = $sPathPrefix . $sFile;

        $sOutFile = $this->sRootPath . "/" . trim($sOutFileName, "\\/");

        if(file_exists($sOutFile)) 
            throw new SetupException("File '$sOutFile' already exists", SetupException::FILE_EXISTS);

        $hF = fopen($sOutFile, "w"); 
        fwrite($hF, "<?php\n");
        
        fwrite($hF, "\$__autoload_classes=");
        fwrite($hF, var_export($aLocalizedModules, true));
        fwrite($hF, ";\n\n");

        fwrite($hF, <<<PHP

spl_autoload_register( function(\$sClass) {
    global \$__autoload_classes;

    if(isset(\$__autoload_classes[\$sClass])) {
        require_once(__DIR__ . "/" . \$__autoload_classes[\$sClass]);
        return true;
    }

    return false;
} );

PHP
);


        fclose($hF);

        return true;
    }



//==============================================================================
// Privates
//==============================================================================
    private function __construct() { }
}






