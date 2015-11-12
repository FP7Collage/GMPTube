// ActionScript file
// ActionScript file
package com.util
{
    public class DateTimeUtils
    {
        public static function fromEpochSeconds(seconds:int):Date
        {
            var result:Date = new Date();
            result.setTime(seconds * 1000);
            return result;
        }
        
        public static function formatOffset(seconds:int):String
        {
            // Note: does not handle hours.
            var result:String = (seconds % 60).toString();
            if (result.length == 1)
            {
                result = Math.floor(seconds / 60).toString() + ":0" + result;
            }
            else
            {
                result = Math.floor(seconds / 60).toString() + ":" + result;
            }
            return result;
        }
        
        public static function formatOffsetNumber(seconds:int):Number
        {
            // Note: does not handle hours.
            var result:Number = (seconds % 60);
            if (result.toString().length == 1)
            {
                result = Math.floor(seconds / 60) +  result;
            }
            else
            {
                result = Math.floor(seconds / 60) + result;
            }
            return result;
        }
    }
}