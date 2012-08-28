<?php
namespace Conductor\Converter;

use Conductor\Util\PEARPackageFilev2;

class Package2XmlToComposer
{
    protected $_xml;
    protected $_data;
    
    protected $_type = 'library';
    protected $_name;
    protected $_keywords;
    protected $_license;
    protected $_homepage;
    protected $_dependency_map = array();
    protected $_support = array();
    protected $_autoload = array();
    protected $_include_path = array();
    protected $_bin_files;
    
    public function __construct($package2file)
    {
        if (! file_exists($package2file)) {
            throw new \RuntimeException('Could not find ' . $package2file);
        }
        $this->_xml = file_get_contents($package2file);
    }
    
    /**
     * Set the name of the package to put in composer.json
     * 
     * If not set, the channel suggestedalias will be combined with lowercase
     * package name.
     * 
     * @param string $name 
     * @return self
     */
    public function setName($name)
    {
        $this->_name = strval($name);
        return $this;
    }
    
    /**
     * Set the type of composer package. Defaults to 'library'
     * 
     * @param string
     * @return self
     */
    public function setType($type)
    {
        $this->_type = strval($type);
        return $this;
    }
    
    /**
     * Set keywords which will be picked up by Packagist and/or other
     * package search tools.
     * 
     * @param array $keywords
     * @return self
     */
    public function setKeywords($keywords)
    {
        $this->_keywords = (array) $keywords;
        return $this;
    }

    /**
     * Set keywords which will be picked up by Packagist and/or other
     * package search tools.
     * 
     * @param array $keywords
     * @return self
     */
    public function setSupportInfo($support)
    {
        $support = (array) $support;
        $this->_support = array();
        $valid = array('email', 'issues', 'forum', 'wiki', 'irc', 'source');
        foreach ($support as $key => $val) {
            if (in_array($key, $valid)) {
                $this->_support[$key] = $val;
            }
        }
        
        return $this;
    }

    /**
     * Set homepage to use in composer.json. Defaults to channel if not set.
     * package search tools.
     * 
     * @param string $homepage
     * @return self
     */
    public function setHomepage($homepage)
    {
        $this->_homepage = strval($homepage);
        return $this;
    }
    
    /**
     * Set SPDX license string. If omitted, license value from package.xml will 
     * be used.
     * 
     * @see http://www.spdx.org/licenses/
     * @param string
     * @return self
     */
    public function setLicense($license)
    {
        $this->_license = strval($license);
        return $this;
    }

    /**
     * Set a name mapping to dependencies. Naming conventions can vary 
     * between PEAR-style and composer/github style.
     * 
     * @param array $map
     * @return self
     */
    public function setDependencyMap($map)
    {
        $this->_dependency_map = (array) $map;
        return $this;
    }
    
    /**
     * Set up any autoload configuration necessary
     * 
     * @param array $config
     * @return self
     */
    public function setAutoload($config)
    {
        $this->_autoload = (array) $config;
        return $this;
    }
    
    /**
     * If you must, set up any include paths, relative to vendor dir
     * 
     * @param array $list of paths
     * @return self
     */
    public function setIncludePath($list)
    {
        $this->_include_path = (array) $list;
        return $this;
    }
    
    /**
     * Set bin-files list
     *
     * @todo attempt to glean this from package.xml with override support
     * @param array $files
     * @return self
     */
    public function setBinFiles($files)
    {
        $this->_bin_files = (array) $files;
        return $this;
    }
    
        
    public function convert($output_file = null)
    {
        $pv2 = new PEARPackageFilev2($this->_xml);
        $data = $pv2->parse();
        $this->_data = $data;
        
        if (empty($this->_name) && isset($data['channel'])) {
            $suggested_alias = $this->_getChannelSuggestedAlias($data['channel']);
            $pkgname = strtolower($data['name']);
            $pkgname = str_replace('_', '-', $pkgname);
            $this->_name = strtolower($suggested_alias . '/' . $pkgname);
        }
        
        // assemble human-readable composer.json
        $tab = '    ';
        $j = "{\n";
        $j .= $tab . '"name": "' . $this->_name . "\",\n";

        // short package.xml summaries are what composer means for descriptions
        if (isset($data['summary'])) {
            $j .= $tab . '"description": "'. $data['summary'] . "\",\n";
        }

        if (! empty($this->_type)) {
            $j .= $tab . '"type": "' . $this->_type . "\",\n";
        }
      
        if (! empty($this->_keywords)) {
            $j .= $tab . '"keywords": [' . "\n";
            foreach ($this->_keywords as $kw) {
                $j .= $tab . $tab . "\"$kw\",\n";
            }
            
            $j = rtrim($j, ",\n") . "\n$tab],\n";
        }

        if (! empty($this->_homepage)) {
            $homepage = $this->_homepage;
        } elseif (isset($data['channel'])) {
            $homepage = 'http://' . $data['channel'];
        }
        $j .= $tab . '"homepage": "'.$homepage."\",\n";
        
        if (! empty($this->_license)) {
            $license = $this->_license;
        } elseif (isset($data['license']['type'])) {
            $license = $data['license']['type'];
        }
        $j .= $tab . '"license": "'.$license."\",\n";
        
        $j .= $tab . '"authors": [' . "\n";
        $author_types = array('lead', 'developer', 'contributor', 'helper');
        foreach ($author_types as $atype) {
            if (! empty($data[$atype])) {
                foreach ($data[$atype] as $dev) {
                    $j .= $tab . $tab . "{\n";
                    if (! empty($dev['name'])) {
                        $j .= $tab . $tab . $tab . "\"name\": \"{$dev['name']}\",\n";
                    }
                    if (! empty($dev['email'])) {
                        $j .= $tab . $tab . $tab . "\"email\": \"{$dev['email']}\",\n";
                    }
                    $j .= $tab . $tab . $tab . "\"role\": \"$atype\"\n";
                    $j .= $tab . $tab . "},\n";
                }
                $j = rtrim($j, ",\n") . "\n";
            }
        }
        $j .= $tab . "],\n";

        if (isset($data['version']['release'])) {
            $j .= $tab . '"version": "'. $data['version']['release'] . "\",\n";
        }
        if (isset($data['date'])) {
            $j .= $tab . '"time": "'. $data['date'] . "\",\n";
        }

        if (! empty($this->_support)) {
            $j .= $tab . "\"support\": {\n";
            foreach ($this->_support as $key => $val) {
                $j .= $tab . $tab . "\"$key\": \"$val\",\n";
            }
            $j = rtrim($j, ",\n") . "\n";
            $j .= $tab . "},\n";
        }

        
        // requirements
        $deptypes = array('required' => 'require', 'optional' => 'suggest');
        foreach ($deptypes as $pear_deptype => $composer_deptype) {
            if (! empty($data['dependencies'][$pear_deptype])) {
                $j .= $tab . "\"{$composer_deptype}\": {\n";
                foreach ($data['dependencies'][$pear_deptype] as $req) {
                    if ($req['dep'] == 'pearinstaller') {
                        continue;
                    }
                    if ($req['dep'] == 'php') {
                        $j .= $tab . $tab . "\"php\": \"" . $this->_getDepVersionString($req) . "\",\n";
                    }
                    if ($req['dep'] == 'extension') {
                        $j .= $tab . $tab . "\"ext-{$req['name']}\": \"" . $this->_getDepVersionString($req) . "\",\n";
                    }
                    if ($req['dep'] == 'package') {
                    
                        $reqname = '';
                        // is it in the map?
                        $reqkey = '';
                        if (isset($req['channel'])) {
                            $reqkey .= $req['channel'];
                        } else {
                            $reqkey .= $data['channel'];
                        }
                        $reqkey .= '/' . $req['name'];
                        if (isset($this->_dependency_map[$reqkey])) {
                            $reqname = $this->_dependency_map[$reqkey];
                        } else {
                            $reqname = 'pear-' . $reqkey;
                        }
                    
                        $j .= $tab . $tab . "\"$reqname\": \"" . $this->_getDepVersionString($req) . "\",\n";
                    }
                }
                $j = rtrim($j, ",\n") . "\n"; 
                $j .= $tab . "},\n";
            }
//            $j = rtrim($j, ",\n") . "\n"; 
        }
        
        // some defaults
        $j .= $tab . "\"config\": {\n";
        $j .= $tab . $tab . "\"bin-dir\": \"bin\"\n";
        $j .= $tab . "},\n";
        
        if (! empty($this->_bin_files)) {
            $j .= $tab . "\"bin\": [\n";
            foreach ($this->_bin_files as $file) {
                $j .= $tab . $tab . "\"$file\",\n";
            }
            $j = rtrim($j, ",\n") . "\n";
            $j .= $tab . "],\n";
        }
        
        if (! empty($this->_autoload)) {
            $j .= $tab . "\"autoload\": {\n";
            foreach ($this->_autoload as $type => $list) {
                if ($type == 'psr-0') {
                    $j .= $tab . $tab . "\"psr-0\": {\n";
                    foreach ($list as $key => $val) {
                        $j .= $tab . $tab . $tab . "\"$key\": \"";
                        if ($val === null) {
                            $j .= "\",\n";
                        } else {
                            $j .= $val . "\",\n";
                        }
                    }
                    $j = rtrim($j, ",\n") . "\n";
                    $j .= $tab . $tab . "},\n";
                } elseif ($type == 'files' || $type == 'classmap') {
                    $j .= $tab . $tab . "\"$type\": [\n";
                    foreach ($list as $val) {
                        $j .= $tab . $tab . $tab . "\"$val\",\n";
                    }
                    $j = rtrim($j, ",\n") . "\n";
                    $j .= $tab . $tab . "],\n";
                }
            }
            $j = rtrim($j, ",\n") . "\n";
            $j .= $tab . "},\n";
        }
        
        if (! empty($this->_include_path)) {
            $j .= $tab . "\"include-path\": [\n";
            foreach ($this->_include_path as $val) {
                if ($val === null) {
                    $j .= $tab . $tab . "\"\",\n";
                } else {
                    $j .= $tab . $tab . "\"$val\",\n";
                }
            }
            $j = rtrim($j, ",\n") . "\n";
            $j .= $tab . "]\n";
        }
        
        // wrap it up
        $j = rtrim($j, ",\n") . "\n";
        $j .= "}\n";
        
        if ($output_file === null) {
            return $j;
        } elseif ($output_file === true) {
            $cwd = getcwd();
            file_put_contents($cwd.'/composer.json', $j);
        } else {
            file_put_contents($output_file, $j);
        }
    }
    
    protected function _getDepVersionString($req)
    {
        $out = array();
        if (! empty($req['min'])) {
            $v = '>='.$req['min'];
            if ($req['dep'] == 'package' && $this->_data['stability']['release'] == 'stable') {
                $v .= '@stable';
            }
            $out[] = $v;
        }
        if (! empty($req['max'])) {
            $v = '<='.$req['max'];
            if ($req['dep'] == 'package' && $this->_data['stability']['release'] == 'stable') {
                $v .= '@stable';
            }
            $out[] = $v;
        }
        
        if (! empty($out)) {
            $ret = join(',', $out);
        } else {
            $ret = '*';
        }
        return $ret;
    }
    
    protected function _getChannelSuggestedAlias($channel)
    {
        $channelxml = file_get_contents('http://' . $channel . '/channel.xml');
        $channel = new \SimpleXMLElement($channelxml);

        return (string) $channel->suggestedalias;
    }
}