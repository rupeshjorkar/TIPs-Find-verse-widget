const BASE_URL = 'https://tips.translation.bible/wp-json/v1/bible';

const tipsApi = {
	// Verse Listing Page API
	findVerse: async (verse) => {
		try {
			const res = await fetch(`${BASE_URL}/find-verse/?verse=${encodeURIComponent(verse)}`);
			if (!res.ok) throw new Error(`Error ${res.status}: Failed to fetch verse`);
			return await res.json();
		} catch (error) {
			console.error('findVerse error:', error);
			return { error: error.message };
		}
	},

	// Fetch related story by verse slug
	fetchVerseRelatedStory: async (verseSlug, page) => {
		try {
			const res = await fetch(`${BASE_URL}/tip_verse?verseId=${encodeURIComponent(verseSlug)}&paged=${page}`);
			if (!res.ok) throw new Error(`Error ${res.status}: Failed to fetch related story`);
			return await res.json();
		} catch (error) {
			console.error('fetchVerseRelatedStory error:', error);
			return { error: error.message };
		}
	},

	// Fetch story detail by story slug
	fetchStoryDetail: async (storySlug) => {
		try {
			const res = await fetch(`${BASE_URL}/story/?storyId=${encodeURIComponent(storySlug)}`);
			if (!res.ok) throw new Error(`Error ${res.status}: Failed to fetch story detail`);
			return await res.json();
		} catch (error) {
			console.error('fetchStoryDetail error:', error);
			return { error: error.message };
		}
	},

	// searchStory
	fetchSearchStory: async (text, page) => {
		try {
			const res = await fetch(`${BASE_URL}/search/?Id=${encodeURIComponent(text)}&paged=${page}`);
			if (!res.ok) throw new Error(`Error ${res.status}: Failed to fetch searchstory`);
			return await res.json();
		} catch (error) {
			console.error('findSearch error:', error);
			return { error: error.message };
		}
	},

	// Fetch Source Related Story
	fetchSourceRelatedStory: async (verseSlug, page) => {
		try {
			const res = await fetch(`${BASE_URL}/tip_source/?sourceId=${encodeURIComponent(verseSlug)}&paged=${page}`);
			if (!res.ok) throw new Error(`Error ${res.status}: Failed to Source Related Story`);
			return await res.json();
		} catch (error) {
			console.error('fetchSourceRelatedStory error:', error);
			return { error: error.message };
		}
	},

	// Fetch Category List
	fetchCategoryList: async () => {
		try {
			const res = await fetch(`${BASE_URL}/pick_category`);
			if (!res.ok) throw new Error(`Error ${res.status}: Failed to Pick Category`);
			return await res.json();
		} catch (error) {
			console.error('fetchCategoryList error:', error);
			return { error: error.message };
		}
	},

	//Fetch Category Realated Story
	fetchCategoryRelatedStory: async (categorySlug, page) => {
		try {
			const res = await fetch(`${BASE_URL}/category_story/?categoryId=${encodeURIComponent(categorySlug)}&paged=${page}`);;
			if (!res.ok) throw new Error(`Error ${res.status}: Failed to Category Related Story`);
			return await res.json();
		} catch (error) {
			console.error('fetchCategoryRelatedStory error:', error);
			return { error: error.message };
		}
	},

	//Tree View Page
	fetchTreeView: async (termId) => {
		try {
			const res = await fetch(`${BASE_URL}/tree-view?termId=${encodeURIComponent(termId)}`);;
			if (!res.ok) throw new Error(`Error ${res.status}: Failed to Fetch Tree View`);
			return await res.json();
		} catch (error) {
			console.error('fetchTreeView error:', error);
			return { error: error.message };
		}
	},
}

export default tipsApi;