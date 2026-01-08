import bwipjs from 'bwip-js';

// Function to generate the barcode
export const generateBarcode = async (text: string) => {
  try {
    const pngBuffer = await bwipjs.toBuffer({
      bcid: 'qrcode',
      text,
      scale: 2,
      width: 20, // Set width to ensure square shape
      height: 20, // Set height to match width
      includetext: true,
      textxalign: 'center',
    });
    return `data:image/png;base64,${pngBuffer.toString('base64')}`;
  } catch (error) {
    console.error('Error generating barcode:', error);
  }
};
