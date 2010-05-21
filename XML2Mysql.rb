#!/usr/bin/env ruby
if ARGV.length == 0 || ARGV.length ==1
	puts "Usage: XML2Mysql.rb INPUT.XML OUTPUT.sql"
  puts "This ruby script needs two ARGV parameters:"
  puts "  1)input XML file; 2)output SQL data definition file"
	exit 0
end

IfileName= ARGV[0]
OfileName =ARGV[1]

puts "\n"
puts "input file is " + IfileName

Ofile = File.new( OfileName, "w+" )
Te2file = File.new( "Save2Temp324111aa", "w+" )
inFile=File.open( IfileName, "r" ) 

TabCol =Array.new

#(/(foo|bar|baz)/, "baq")

while inFile.gets do  
  
  if ~/<system><title>((.*?))<\/title>/    
      # puts $1
      $Tit1=$1.gsub(/\s+/, "_")
      $Tit2=""
  end
   
      if ~/<part><title>((.*?))<\/title>/
        #puts $1
        $Tit2 = $1.gsub(/\s+/, "_")   
      end
      
      if ~/<choice/         
         if ~/\>((.*?))<\/choice>/         
           #$Sub3=$1.gsub(/\s+/, "_")          
           @Sub3=$1.gsub(/\s+/, "_")
           @choicetype = "0"
           
          # if @Sub3 = ~/other/i
          #    @choicetype = "1"
          #end           
            
         end
         
        if $Tit2 != ""
          @TabCol =  $Tit1 +"_"  + $Tit2 + "_" + @Sub3 + ":" + @choicetype
        else
           @TabCol =  $Tit1 + "_" + @Sub3 + ":" + @choicetype 
        end
         
        Te2file.puts @TabCol
        
        
      elsif ~/<text/
          if ~/\>((.*?))<\/text>/
             @Sub3=$1.gsub(/\s+/, "_")
             @choicetype = "1" 
           end
           
          if $Tit2 != ""
            @TabCol =  $Tit1 +"_"  + $Tit2 + "_" + @Sub3 + ":" + @choicetype
          else
            @TabCol =  $Tit1 + "_" + @Sub3 + ":" + @choicetype 
          end
         
          Te2file.puts @TabCol
        
      end
    
  end
  
inFile.close
Te2file.close


Ofile.puts "CREATE TABLE IF NOT EXISTS PX4Urol_XML"
Ofile.puts "("
Ofile.puts "  `ID` int(11) NOT NULL auto_increment,";
Ofile.puts "  `PatientName` char(45) NOT NULL default '',";
Ofile.puts "  `PT_gender` tinyint(3) unsigned NOT NULL default '0',";
Ofile.puts "  `Date` NOT NULL default '0000-00-00',";
Ofile.puts "  `RequestedBy` char(35) NULL default '',\n";
Ofile.puts "  `MRN`  int(9) default '',\n"; 


f = File.open("Save2Temp324111aa","r") 
f.each do |line|
  #puts line
 
    v1,v2=line.chomp.split(/:/)
  
    if  v2 =~ /1$/
       #split 
      Ofile.puts "  `" + v1 + "` VARCHAR(80) NULL,"
    elsif  v2 =~ /2$/
      Ofile.puts "  `" + v1 + "` int(5) default ''," 
    else
      Ofile.puts "  `"+ v1 + "` BINARY NULL,"
    end    
  end  

Ofile.puts  "  PRIMARY KEY  (`ID`), ";
Ofile.puts  "  KEY (`PatientName`) " ;
Ofile.puts  "\)TYPE=MyISAM;";

Ofile.close
f.close

puts "\n"
puts "output file is " + OfileName
puts "\n"

File.delete( "Save2Temp324111aa" )

