import * as React from "react";
import { Link, useParams } from "react-router";
import { 
  History, ArrowLeft, Calendar, Sparkles, CheckCircle2, 
  User, Heart, MessageCircle, Share2, UserPlus, Check 
} from "lucide-react";
import { mockPosts } from "../data/mock-data"; 
import { Avatar, AvatarFallback, AvatarImage } from "../components/ui/avatar";
import { BookmarkButton } from "../components/bookmark-button"; // 🚀 Yeni butonumuz

export function PostDetailPage() {
  const { slug } = useParams(); 
  const post = mockPosts.find((p) => p.slug === slug);
  
  const [activeVersion, setActiveVersion] = React.useState<any>(null);
  const [isLiked, setIsLiked] = React.useState(false);
  const [likes, setLikes] = React.useState(142);
  const [isFollowing, setIsFollowing] = React.useState(false);
  const [showComments, setShowComments] = React.useState(false);

  React.useEffect(() => {
    if (post && post.versions) {
      const current = post.versions.find((v: any) => v.isCurrent) || post.versions[0];
      setActiveVersion(current);
    }
  }, [post]);

  if (!post) {
    return (
      <div className="flex-1 flex flex-col items-center justify-center min-h-screen text-slate-500">
        <h2 className="text-3xl font-serif italic mb-4">Yazı bulunamadı</h2>
        <Link to="/" className="text-teal-600 hover:underline font-medium">Ana Sayfaya Dön</Link>
      </div>
    );
  }

  const formatContent = (text: string) => {
    return text.split('\n').map((line) => {
      if (line.startsWith('# ')) return `<h1 class="text-4xl font-serif italic mt-8 mb-6">${line.slice(2)}</h1>`;
      if (line.startsWith('## ')) return `<h2 class="text-3xl font-serif italic mt-6 mb-4">${line.slice(3)}</h2>`;
      if (line.trim() === '') return `<br/>`;
      return `<p class="mb-4 leading-relaxed">${line}</p>`;
    }).join('');
  };

  const displayContent = activeVersion?.isCurrent 
    ? formatContent(post.content)
    : `<div class="p-6 bg-amber-50/50 border border-amber-200 rounded-2xl mb-8 text-amber-800 text-sm flex flex-col gap-2">
         <strong class="text-base font-serif italic">⚠️ Eski Sürümü Görüntülüyorsunuz</strong>
         <span>Şu an v${activeVersion?.version} sürümündesiniz.</span>
       </div>` + formatContent(post.content).substring(0, 400) + "...";

  return (
    <div className="flex-1 flex flex-col bg-white dark:bg-slate-950 font-sans min-h-screen">
      
      {/* KOD BLOKLARI İÇİN ÖZEL STİL */}
      <style dangerouslySetInnerHTML={{__html: `
        .prose pre { background: #0f172a !important; color: #f8fafc !important; padding: 2rem !important; border-radius: 1.5rem !important; }
        .prose code { color: #0d9488 !important; font-weight: 700 !important; }
      `}} />

      <main className="container mx-auto max-w-6xl py-12 px-6 animate-in fade-in duration-700">
        
        {/* ÜST GEZİNTİ VE KAYDET BUTONU */}
        <div className="flex justify-between items-center mb-10">
          <Link to="/" className="inline-flex items-center gap-2 text-slate-400 hover:text-teal-600 transition-colors text-sm font-medium">
            <ArrowLeft className="w-4 h-4" /> Ana Sayfaya Dön
          </Link>
          <div className="flex items-center gap-2">
            <span className="text-[10px] font-bold uppercase tracking-widest text-slate-400 mr-2">Kitaplığına Ekle</span>
            <BookmarkButton /> {/* 🚀 İŞTE BURADA! */}
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-16">
          
          {/* SOL TARAF: İÇERİK */}
          <div className="lg:col-span-8 space-y-10">
            <header>
              <div className="flex items-center gap-3 text-teal-600 font-bold text-[10px] uppercase tracking-[0.2em] mb-6">
                <Sparkles className="w-4 h-4" /> <span>{post.category}</span>
              </div>
              <h1 className="font-serif text-5xl md:text-7xl italic tracking-tighter text-slate-950 dark:text-white leading-[1.1] mb-8">
                {post.title}
              </h1>
              
              <div className="flex items-center justify-between py-6 border-y border-slate-100 dark:border-slate-800">
                <div className="flex items-center gap-4">
                  <Avatar className="h-12 w-12 border-2 border-white shadow-sm">
                    <AvatarImage src={post.author.avatar} />
                    <AvatarFallback><User /></AvatarFallback>
                  </Avatar>
                  <div>
                    <div className="flex items-center gap-3">
                      <p className="font-bold text-slate-900 dark:text-white">{post.author.name}</p>
                      <button 
                        onClick={() => setIsFollowing(!isFollowing)}
                        className={`text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full transition-all ${
                          isFollowing ? "bg-slate-100 text-slate-400" : "bg-teal-50 text-teal-700 hover:bg-teal-100"
                        }`}
                      >
                        {isFollowing ? <span className="flex items-center gap-1"><Check className="w-3 h-3"/> Takipte</span> : "+ Takip Et"}
                      </button>
                    </div>
                    <p className="text-xs text-slate-400">{post.readingTime} dk okuma • {activeVersion?.version} sürümü</p>
                  </div>
                </div>
                <button className="p-3 hover:bg-slate-50 rounded-full transition-colors text-slate-400"><Share2 className="w-5 h-5" /></button>
              </div>
            </header>

            <article className="prose prose-lg max-w-none dark:prose-invert">
              <div className="w-full aspect-video rounded-[3rem] overflow-hidden mb-12 shadow-2xl shadow-teal-500/5 border border-slate-100">
                <img src={post.coverImage} className="w-full h-full object-cover" />
              </div>
              <div 
                className="font-sans text-slate-700 dark:text-slate-300 leading-relaxed"
                dangerouslySetInnerHTML={{ __html: displayContent }} 
              />
            </article>

            {/* ETKİLEŞİM PANELİ */}
            <div className="flex items-center gap-8 py-8 border-t border-slate-100 dark:border-slate-800">
              <button 
                onClick={() => { setIsLiked(!isLiked); setLikes(isLiked ? likes - 1 : likes + 1); }}
                className={`flex items-center gap-3 transition-all group ${isLiked ? 'text-red-500' : 'text-slate-400 hover:text-red-500'}`}
              >
                <div className={`p-4 rounded-full transition-colors ${isLiked ? 'bg-red-50' : 'group-hover:bg-red-50'}`}>
                  <Heart className={`w-7 h-7 ${isLiked ? 'fill-current' : ''}`} />
                </div>
                <span className="text-lg font-bold">{likes} Beğeni</span>
              </button>

              <button 
                onClick={() => setShowComments(!showComments)}
                className="flex items-center gap-3 text-slate-400 hover:text-teal-600 group"
              >
                <div className="p-4 rounded-full group-hover:bg-teal-50 transition-colors">
                  <MessageCircle className="w-7 h-7" />
                </div>
                <span className="text-lg font-bold">24 Yorum</span>
              </button>
            </div>
          </div>

          {/* SAĞ TARAF: VERSİYON GEÇMİŞİ */}
          <aside className="lg:col-span-4">
            <div className="sticky top-28 p-8 bg-slate-50 dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800">
              <h3 className="font-serif text-2xl italic mb-8 flex items-center gap-3 text-slate-900 dark:text-white">
                <History className="w-6 h-6 text-teal-600" /> Yazı Geçmişi
              </h3>
              <div className="space-y-4">
                {post.versions.map((ver: any) => {
                  const isActive = activeVersion?.version === ver.version;
                  return (
                    <button
                      key={ver.version}
                      onClick={() => setActiveVersion(ver)}
                      className={`w-full text-left p-5 rounded-3xl transition-all border ${
                        isActive 
                          ? 'bg-white dark:bg-slate-800 border-teal-500 shadow-xl shadow-teal-500/10 scale-[1.03]' 
                          : 'bg-transparent border-transparent hover:bg-white/50 text-slate-500'
                      }`}
                    >
                      <div className="flex justify-between items-center mb-2">
                        <span className={`font-bold ${isActive ? 'text-teal-600' : 'text-slate-900 dark:text-white'}`}>v{ver.version}</span>
                        {isActive && <CheckCircle2 className="w-4 h-4 text-teal-500" />}
                      </div>
                      <p className="text-[10px] uppercase tracking-widest text-slate-400 mb-2">{new Date(ver.date).toLocaleDateString("tr-TR")}</p>
                      <p className="text-xs leading-relaxed italic">{ver.changeNote}</p>
                    </button>
                  );
                })}
              </div>
            </div>
          </aside>
        </div>
      </main>
    </div>
  );
}