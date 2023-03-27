#include "Exercise.h"

int main()
{
    const int arraySize = 5;

    int array[arraySize] = {4, -1, 5, 3, 2};

    Exercise exercise;

    exercise.average(3, 7);

    exercise.totalArray(array, arraySize);

    exercise.range(array, arraySize);

    exercise.highest(1, 3, 4);

    int a = 5, b = 2;

    exercise.swapValues(a, b);

    exercise.incrementValue(b);

    exercise.sumIsEven(1, 2);

    return 0;
}