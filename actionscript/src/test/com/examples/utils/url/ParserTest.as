package test.com.examples.utils.url {
    import com.examples.utils.url.Parser;
    import flexunit.framework.Assert;
    
    [RunWith("org.flexunit.runners.Parameterized")]
    public class ParserTest {
        public static function ParserData():Array {
            return [
                ["test1=1&test2=3&tm=true", {"test1":"1", "test2":"3", "tm":"true" }]
            ];
        }
        
        [Test(dataProvider="ParserData")]
        public function testParse(kvString:String, expected:Object):void {
            Assert.assertObjectEquals(Parser.parse(kvString), expected);
        }
    }
}