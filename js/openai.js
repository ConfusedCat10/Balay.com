import OpenAI from 'openai.js';
        
function openAI(estName, latitude, longitude) {
  const openai = new OpenAI({
    apiKey: "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",
    });
    
    const completion = openai.chat.completions.create({
    model: "gpt-4o-mini",
    store: true,
    messages: [
        {"role": "user", "content": `Describe in detail the location of \"${estName}\", whose coordinates are (${latitude}, ${longitude}). What are the nearby facilities and amenities? How far it is from certain MSU college buildings and facilities and the commercial center?\n\nGive the address too (from street level, barangay, village).`},
    ],
    });
    
    completion.then((result) => console.log(result.choices[0].message));
}
