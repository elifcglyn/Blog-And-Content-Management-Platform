import { useNavigate } from "react-router";
import { Avatar, AvatarFallback, AvatarImage } from "./ui/avatar";
import { Badge } from "./ui/badge";
import { Card, CardContent } from "./ui/card";
import { Clock } from "lucide-react";

interface PostCardProps {
  id: string;
  title: string;
  slug: string;
  excerpt: string;
  category: string;
  author: {
    name: string;
    avatar: string;
  };
  coverImage: string;
  publishedAt: string;
  readingTime: number;
}

export function PostCard({
  id,
  title,
  slug,
  excerpt,
  category,
  author,
  coverImage,
  publishedAt,
  readingTime,
}: PostCardProps) {
  const navigate = useNavigate();

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
      year: "numeric",
    });
  };

  return (
    <Card
      className="overflow-hidden cursor-pointer hover:shadow-lg transition-shadow group"
      onClick={() => navigate(`/post/${slug}`)}
    >
      {/* Cover Image */}
      <div className="aspect-video overflow-hidden bg-muted">
        <img
          src={coverImage}
          alt={title}
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        />
      </div>

      <CardContent className="p-6">
        {/* Category Badge */}
        <Badge variant="secondary" className="mb-3">
          {category}
        </Badge>

        {/* Title */}
        <h3 className="text-xl mb-2 line-clamp-2 group-hover:text-primary transition-colors">
          {title}
        </h3>

        {/* Excerpt */}
        <p className="text-muted-foreground text-sm mb-4 line-clamp-2">
          {excerpt}
        </p>

        {/* Author Info */}
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-3">
            <Avatar className="h-8 w-8">
              <AvatarImage src={author.avatar} alt={author.name} />
              <AvatarFallback>{author.name.charAt(0)}</AvatarFallback>
            </Avatar>
            <div>
              <p className="text-sm">{author.name}</p>
              <p className="text-xs text-muted-foreground">
                {formatDate(publishedAt)}
              </p>
            </div>
          </div>

          {/* Reading Time */}
          <div className="flex items-center gap-1 text-muted-foreground text-xs">
            <Clock className="h-3 w-3" />
            <span>{readingTime} min read</span>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
