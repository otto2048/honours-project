#pragma once

#include <string>

using std::string;
using std::to_string;

class Exercise
{
public:
    float average(float, float);
    int totalArray(int[], int);
    int range(int[], int);
    int highest(int, int, int);
    void swapValues(int&, int&);
    void incrementValue(int&);
    bool goodDinner(int, bool);
    void filterData(int, int, int[], int);
    bool sumIsEven(int, int);
    bool testFactors(int, int, int);
};