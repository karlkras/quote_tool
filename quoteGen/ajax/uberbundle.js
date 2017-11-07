// JavaScript Document

function uberBundle(langCount, bundleType, srcArray, tgtArray, newValue)
{

    for (srcIdx in srcArray)
    {
        for (tgtIdx in tgtArray)
        {
            rollup(bundleType, tgtIdx, srcArray[srcIdx], tgtArray[tgtIdx], newValue);
        }
    }
}