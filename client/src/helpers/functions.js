export const randomInteger = (length) => {
	let min, max;
	min = +(1 + Array(length).join("0"));
	max = +`${min}0`;
	return Math.floor(Math.random() * (max - min)) + min;
};
