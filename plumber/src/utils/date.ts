export const getMinutesDifference = (date1: Date | string, date2: Date | string): number => {
  return Math.floor((new Date(date1).getTime() - new Date(date2).getTime()) / 60000);
};
