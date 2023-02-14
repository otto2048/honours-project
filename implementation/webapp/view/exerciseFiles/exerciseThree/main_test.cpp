#include "gtest/gtest.h"
#include <iostream>

int main(int argc, char **argv) {
  ::testing::InitGoogleTest(&argc, argv);

  const int rv = RUN_ALL_TESTS();

  std::cout << ::testing::UnitTest::GetInstance()->failed_test_count() << std::endl;

  return rv;
}