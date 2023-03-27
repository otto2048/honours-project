#pragma once

#include <string>

using std::string;
using std::to_string;

class Exercise
{
public:
    void average(float, float);
    void totalArray(int[], int);
    void range(int[], int);
    int highest(int, int, int);
    void swapValues(int&, int&);
    void incrementValue(int);
    bool goodDinner(int, bool);
    void filterData(int, int, int[], int);
    bool sumIsEven(int, int);
};