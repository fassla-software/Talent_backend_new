import PlumberCategory from '../plumberCategory/plumber-category.model';

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const calcUserPoints = (items: any[]) => {
  let sum = 0;
  items.forEach(itm => {
    const item = JSON.parse(JSON.stringify(itm, null, 2));
    console.log({ item });
    // Check for subcategory and points existence before calculation
    if (item.subcategory && typeof item.subcategory.points) {
      sum += item.count * item.subcategory.points;
    } else {
      console.warn('Invalid item or subcategory:', item);
    }
  });
  return sum;
};

// export function haversineDistance(lat1: number, lon1: number, lat2: number, lon2: number): number {
//   console.log({ lat1, lon1, lat2, lon2 });
//   const r = 6371;
//   const p = Math.PI / 180;

//   // Haversine formula to calculate the distance
//   const a =
//     0.5 -
//     Math.cos((lat2 - lat1) * p) / 2 +
//     (Math.cos(lat1 * p) * Math.cos(lat2 * p) * (1 - Math.cos((lon2 - lon1) * p))) / 2;

//   const distanceInMeters = 2 * r * Math.asin(Math.sqrt(a)) * 1000; // Distance in meters

//   return parseFloat(distanceInMeters.toFixed(2));
// }

export function haversineDistance(
  lat1: number | string,
  lon1: number | string,
  lat2: number | string,
  lon2: number | string,
): number {
  // Parse and round to 5 decimal places for accuracy
  const roundTo = (value: number, decimals: number) => {
    const factor = Math.pow(10, decimals);
    return Math.round(value * factor) / factor;
  };

  lat1 = roundTo(parseFloat(lat1 as string), 6);
  lon1 = roundTo(parseFloat(lon1 as string), 6);
  lat2 = roundTo(parseFloat(lat2 as string), 6);
  lon2 = roundTo(parseFloat(lon2 as string), 6);

  const r = 6371; // Earth's radius in kilometers
  const p = Math.PI / 180; // Conversion factor from degrees to radians

  const lat1Rad = lat1 * p;
  const lat2Rad = lat2 * p;
  const deltaLat = (lat2 - lat1) * p;
  const deltaLon = (lon2 - lon1) * p;

  const a = Math.sin(deltaLat / 2) ** 2 + Math.cos(lat1Rad) * Math.cos(lat2Rad) * Math.sin(deltaLon / 2) ** 2;

  const distanceInMeters = 2 * r * Math.asin(Math.sqrt(a)) * 1000; // Convert to meters

  console.log({
    lat1,
    lon1,
    lat2,
    lon2,
    deltaLat: lat2 - lat1,
    deltaLon: lon2 - lon1,
    distanceInMeters,
  });
  return distanceInMeters < 1 ? 0 : parseFloat(distanceInMeters.toFixed(2));
}

export const getAllParents = (allCategories: PlumberCategory[], categoryId: number) => {
  const parentCategories = [];
  let currentCategory = allCategories.find(category => category.id === categoryId);

  while (currentCategory && currentCategory.parent_id !== null) {
    const parentCategory = allCategories.find(category => category.id === currentCategory?.parent_id);
    if (parentCategory) {
      parentCategories.push({ id: parentCategory.id, name: parentCategory.name });
      currentCategory = parentCategory;
    } else {
      break;
    }
  }
  return parentCategories;
};

// export function haversineDistance(lat1: number, lon1: number, lat2: number, lon2: number) {
//   const toRadians = (degree: number) => (degree * Math.PI) / 180;

//   const R = 6371; // Radius of the Earth in kilometers
//   const φ1 = toRadians(lat1); // Latitude 1 in radians
//   const φ2 = toRadians(lat2); // Latitude 2 in radians
//   const Δφ = toRadians(lat2 - lat1); // Difference in latitude
//   const Δλ = toRadians(lon2 - lon1); // Difference in longitude

//   const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
//   const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

//   return R * c * 1000; // Distance in meters
// }
